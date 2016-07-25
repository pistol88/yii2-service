<?php
namespace pistol88\service\controllers;

use Yii;
use pistol88\service\models\Category;
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

class PriceController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
				'only' => ['create', 'update', 'index', 'delete', 'order'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ],
        ];
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
        
        $services = Service::find()->orderBy('sort DESC, id ASC')->all();
        $complexes = Complex::find()->orderBy('sort DESC, id ASC')->all();
        $categories = Category::find()->orderBy('sort DESC, id ASC')->all();
        $priceModel = new Price;
        
        return $this->render('index', [
            'services' => $services,
            'categories' => $categories,
            'complexes' => $complexes,
            'priceModel' => $priceModel,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionOrder()
    {
        $services = Service::find()->orderBy('sort DESC, id ASC')->all();
        $categories = Category::find()->orderBy('sort DESC, id ASC')->all();
        $complexes = Complex::find()->orderBy('sort DESC, id ASC')->all();
        
        $priceModel = new Price;
        $orderModel = new Order;
        
        $paymentTypes = ArrayHelper::map(PaymentType::find()->orderBy('order DESC')->all(), 'id', 'name');
        $shippingTypes = ArrayHelper::map(ShippingType::find()->orderBy('order DESC')->all(), 'id', 'name');

        return $this->render('order', [
            'services' => $services,
            'complexes' => $complexes,
            'categories' => $categories,
            'orderModel' => $orderModel,
            'priceModel' => $priceModel,
            'paymentTypes' => $paymentTypes,
            'shippingTypes' => $shippingTypes,
        ]);
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
}
