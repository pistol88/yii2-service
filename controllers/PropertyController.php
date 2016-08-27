<?php
namespace pistol88\service\controllers;

use yii;
use pistol88\service\models\Property;
use pistol88\service\models\property\PropertySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class PropertyController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = PropertySearch::find()->all();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'module' => $this->module,
        ]);
    }

    public function actionCreate()
    {
        $model = new Property;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $module = $this->module;

            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'module' => $this->module,
            ]);
        }
    }
    
    public function actionAjaxCreate()
    {
        $model = new Property();

        $json = [];
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $json['result'] = 'success';
            $json['id'] = $model->id;
        } else {
            $json['result'] = 'fail';
            $json['errors'] = current($model->getFirstErrors());
        }
        
        return json_encode($json);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $module = $this->module;

            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', ['model' => $model, 'module' => $this->module]);
        }
    }

    public function actionCreateWidget()
    {
        $model = new Property;

        $json = [];
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $json['result'] = 'success';
            $json['promocode'] = $model->code;
        } else {
            $json['result'] = 'fail';
            $json['errors'] = current($model->getFirstErrors());
        }
        
        return json_encode($json);
    }
    
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', ['model' => $model, 'module' => $this->module]);
    }
    
    public function actionDelete($id)
    {
        if($model = $this->findModel($id)) {
            $this->findModel($id)->delete();
            $module = $this->module;
        }
        return $this->redirect(['index']);
    }

    public function actionGetAjaxList($clientId)
    {
        $list = Property::find()->where(['client_id' => $clientId])->asArray()->all();
        
        $json = [
            'result' => 'success',
            'list' => $list,
        ];
        
        return json_encode($json);
    }
    
    protected function findModel($id)
    {
        $model = new Property;
        
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested client does not exist.');
        }
    }
}
