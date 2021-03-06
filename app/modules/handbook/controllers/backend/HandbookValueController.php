<?php
namespace handbook\controllers\backend;

use Yii;
use handbook\forms\HandbookValueSearch;
use handbook\models\Handbook;
use handbook\models\HandbookValue;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Exception;
use yii\web\ServerErrorHttpException;
use yii\web\NotFoundHttpException;

/**
 * Управление значениями справочника
 */
class HandbookValueController extends \app\components\BackendController
{
    public function getSubstanceName()
    {
        return Yii::t('backend/handbook', 'of value');
    }

    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge([
            'access' => [
                'rules' => [
                    [
                        'actions' => ['index', 'update'],
                        'allow' => true,
                        'verbs' => ['GET'],
                        'roles' => ['backendViewHandbookValues'],
                    ],
                    [
                        'actions' => ['update', 'delete'],
                        'allow' => true,
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['backendUpdateHandbookValues'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['backendCreateHandbookValues'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ], parent::behaviors());
    }

    /**
     * Получить справочник по символьному коду.
     *
     * @param string $code символьный код справочника
     * @return \handbook\models\Handbook
     * @throws NotFoundHttpException
     */
    protected function getHandbook($code)
    {
        $ret = Handbook::find()->where(['code' => $code])->one();

        if (!($ret instanceof Handbook)) {
            throw new NotFoundHttpException();
        }

        return $ret;
    }

    /**
     * Создание новой записи
     */
    public function actionCreate($code)
    {
        $handbook = $this->getHandbook($code);

        $model = new HandbookValue();
        $model->handbook_code = $handbook->code;

        if (Yii::$app->request->isAjax && $model->load($_POST)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load($_POST) && $model->validate()) {
            try {
                if (!$model->save()) {
                    throw new Exception();
                }

                Yii::$app->serviceMessage->setMessage('success', Yii::t('backend/handbook', 'Success create handbook value'));

                if (isset($_POST['apply'])) {
                    return $this->refresh();
                }
                else {
                    return $this->redirect(['index', 'code' => $handbook->code]);
                }
            }
            catch (Exception $ex) {
                Yii::$app->serviceMessage->setMessage('danger', Yii::t('backend/handbook', 'Error create a handbook value'));
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Редактирование записи
     */
    public function actionUpdate($code, $id)
    {
        $handbook = $this->getHandbook($code);

        $model = $this->findModel($handbook->code, $id);
        $model->handbook_code = $handbook->code;

        if (Yii::$app->request->isAjax && $model->load($_POST)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load($_POST) && $model->validate()) {
            try {
                if (!$model->save()) {
                    throw new Exception();
                }

                Yii::$app->serviceMessage->setMessage('success', Yii::t('backend/handbook', 'Success update value'));

                if (isset($_POST['apply'])) {
                    return $this->refresh();
                }
                else {
                    return $this->redirect(['index', 'code' => $handbook->code]);
                }
            }
            catch (Exception $ex) {
                Yii::$app->serviceMessage->setMessage('danger', Yii::t('backend/handbook', 'Error update a value'));
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Удаление значения
     */
    public function actionDelete($code, $id)
    {
        $handbook = $this->getHandbook($code);

        $model = $this->findModel($handbook->code, $id);

        try {
            $model->delete();

            return $this->redirect(['index', 'code' => $handbook->code]);
        } catch (Exception $ex) {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * Список значений
     */
    public function actionIndex($code)
    {
        $handbook = $this->getHandbook($code);

        $searchModel = new HandbookValueSearch();
        $searchModel->handbook_code = $handbook->code;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Возвращает искомую модель
     * @param string $code символьный код справочника
     * @param string $id
     * @return \app\components\ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findModel($code, $id)
    {
        $model = HandbookValue::find()->where([
            'id' => $id,
            'handbook_code' => $code,
        ])->one();

        if (empty($model)) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}