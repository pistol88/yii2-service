<?php
namespace pistol88\service\controllers;

use Yii;
use pistol88\service\models\Category;
use pistol88\service\models\CustomService;
use pistol88\service\models\Service;
use pistol88\service\models\Complex;
use pistol88\service\models\Price;
use pistol88\order\models\Order;
use pistol88\order\models\PaymentType;
use pistol88\order\models\ShippingType;
use pistol88\service\models\price\PriceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\helpers\Url;

class PriceController extends Controller
{
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ]
        ];

        if($this->module->cache) {
            $caches = [
                'pageCache' => [
                    'class' => 'yii\filters\PageCache',
                    'only' => ['order'],
                    'duration' => $this->module->cachePeriod,
                    'dependency' => [
                        'class' => 'yii\caching\DbDependency',
                        'sql' => 'SELECT COUNT(*) FROM service_price',
                    ],
                    'variations' => [
                        yii::$app->request->get(),
                        yii::$app->request->post(),
                        yii::$app->user->id,
                        yii::$app->request->cookies->get('service-order-type'),
                        yii::$app->cart->hash,
                    ]
                ],
                'pageCache2' => [
                    'class' => 'yii\filters\PageCache',
                    'only' => ['get-prices', 'get-categories'],
                    'duration' => $this->module->cachePeriod,
                    'variations' => [
                        yii::$app->request->get(),
                        yii::$app->request->post(),
                    ]
                ],
            ];

            $behaviors = array_merge($behaviors, $caches);
        }

        return $behaviors;
    }

    public function actionIndex()
    {
        $searchModel = new PriceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if($prices = yii::$app->request->post('price')) {
            foreach($prices as $categoryId => $types) {
                foreach($types as $type => $services) {
                    foreach($services as $serviceId => $price) {
                        $service = $type::findOne($serviceId);

                        if(!$priceModel = Price::find()->tariff($categoryId, $service)->one()) {
                            $priceModel = new Price;
                            $priceModel->category_id = $categoryId;
                            $priceModel->service_id = $serviceId;
                            $priceModel->service_type = $type;
                        }
                        $priceModel->price = $price;
                        $priceModel->save();
                    }
                }
            }
        }

        $priceModel = new Price;

        $prices = [];

        foreach($priceModel::find()->all() as $price) {
            $prices[$price->service_type][$price->category_id][$price->service_id] = $price;
        }

        $organization = false;
        
        if(yii::$app->has('organization')) {
            $organization = yii::$app->organization->get();
        }
        
        if($organization) {
            $services = Service::find()->where('(calculator = "" OR calculator IS NULL) AND organization_id = :org_id', [':org_id' => $organization->id])->orderBy('sort DESC, id ASC')->all();
            $categories = Category::find()->where(['organization_id' => $organization->id])->orderBy('sort DESC, id ASC')->all();
            $complexes = Complex::find()->where(['organization_id' => $organization->id])->orderBy('sort DESC, id ASC')->all();
        } else {
            $services = Service::find()->orderBy('sort DESC, id ASC')->all();
            $complexes = Complex::find()->orderBy('sort DESC, id ASC')->all();
            $categories = Category::find()->where('parent_id IS NULL OR parent_id = 0')->orderBy('sort DESC, id ASC')->all();
        }

        if(yii::$app->has('organization')) {
            $organizations = yii::$app->organization->getList();
        } else {
            $organizations = [];
        }
        
        return $this->render('index', [
            'organization' => $organization,
            'organizations' => $organizations,
            'prices' => $prices,
            'services' => $services,
            'categories' => $categories,
            'complexes' => $complexes,
            'priceModel' => $priceModel,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOrder($order_id = false)
    {
        $customServiceModel = new CustomService;

        if ($customServiceModel->load(Yii::$app->request->post()) && $customServiceModel->save()) {
            yii::$app->cart->put($customServiceModel);
            if(yii::$app->request->post('ajax')) {
                die(json_encode(['result' => 'success']));
            } else {
                \Yii::$app->session->setFlash('customServiceBuy', 'В корзине!');
            }
        }

        if($type = yii::$app->request->get('service-order-type')) {
            if(!in_array($type, ['net', 'table'])) {
                return $this->redirect('404');
            }

            yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => 'service-order-type',
                'value' => $type
            ]));
        } else {
            $type = yii::$app->request->cookies->get('service-order-type');

            if(!$type) {
                $type = 'net';
            }
        }

        $organization = false;
        $organizations = [];
        
        if(yii::$app->has('organization')) {
            $organization = yii::$app->organization->get();
            $organizations = yii::$app->organization->getList();
        }
        
        if($organization) {
            $services = Service::find()->where('(calculator = "" OR calculator IS NULL) AND organization_id = :org_id', [':org_id' => $organization->id])->orderBy('sort DESC, id ASC')->all();
            $categories = Category::find()->where(['organization_id' => $organization->id])->orderBy('sort DESC, id ASC')->all();
            $complexes = Complex::find()->where(['organization_id' => $organization->id])->orderBy('sort DESC, id ASC')->all();
        } else {
            $services = Service::find()->orderBy('sort DESC, id ASC')->all();
            $categories = Category::find()->where('parent_id IS NULL OR parent_id = 0')->orderBy('sort DESC, id ASC')->all();
            $complexes = Complex::find()->orderBy('sort DESC, id ASC')->all();
        }

        $calculateServiceModel = Service::find()->where('calculator != ""')->all();

        $priceModel = new Price;
        $orderModel = new Order;

        $paymentTypes = ArrayHelper::map(PaymentType::find()->orderBy('order DESC')->all(), 'id', 'name');
        $shippingTypes = ArrayHelper::map(ShippingType::find()->orderBy('order DESC')->all(), 'id', 'name');

        $prices = [];

        foreach($priceModel::find()->all() as $price) {
            $prices[$price->service_type][$price->category_id][$price->service_id] = $price;
        }

        $this->getView()->registerJs('pistol88.service.propertyListUrl = "'.Url::toRoute(['/service/property/get-ajax-list']).'";');
        $this->getView()->registerJs('pistol88.service.searchClientByIdentUrl = "'.Url::toRoute(['/service/property/get-client-by-property']).'";');

        if($order_id) {
            $order = Order::findOne($order_id);
        } else {
            $order = false;
        }

        return $this->render('order', [
            'order' => $order,
            'organization' => $organization,
            'organizations' => $organizations,
            'type' => $type,
            'customServiceModel' => $customServiceModel,
            'calculateServiceModel' => $calculateServiceModel,
            'prices' => $prices,
            'services' => $services,
            'complexes' => $complexes,
            'categories' => $categories,
            'orderModel' => $orderModel,
            'priceModel' => $priceModel,
            'paymentTypes' => $paymentTypes,
            'shippingTypes' => $shippingTypes,
        ]);
    }

    public function actionGetPrices()
    {
        $categoryId = (int)yii::$app->request->post('id');

        $categoryModel = Category::findOne($categoryId);

        if(yii::$app->has('organization') && $organization = yii::$app->organization->get()) {
            $services = Service::find()->where('(calculator = "" OR calculator IS NULL) AND organization_id = :org_id', [':org_id' => $organization->id])->orderBy('sort DESC, id ASC')->all();
            $complexes = Complex::find()->where(['organization_id' => $organization->id])->orderBy('sort DESC, id ASC')->all();

        } else {
            $services = Service::find()->where('calculator = "" OR calculator IS NULL')->orderBy('sort DESC, id ASC')->all();
            $complexes = Complex::find()->orderBy('sort DESC, id ASC')->all();
        }
        


        $priceModel = new Price;

        $json = [];

        $json['HtmlBlock'] = $this->renderPartial('order-type/net/services', [
            'priceModel' => $priceModel,
            'categoryId' => $categoryId,
            'services' => $services,
            'complexes' => $complexes,
            'categoryModel' => $categoryModel,
        ]);

        die(json_encode($json));
    }

    public function actionGetCategories()
    {
        $categories = Category::find()->where('parent_id IS NULL OR parent_id = 0')->orderBy('sort DESC, id ASC')->all();

        $json = [];

        $json['HtmlBlock'] = $this->renderPartial('order-type/net/categories', [
            'categories' => $categories,
        ]);

        die(json_encode($json));
    }

    public function actionCreate()
    {
        $model = new Price();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Price::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetCalculateServiceFormAjax($id)
    {
        $calculateService = Service::findOne($id);

        return $this->renderAjax('calculate_service_widget_ajax', [
            'name' => $calculateService->name,
            'settings' => $calculateService->settings,
        ]);
    }
}
