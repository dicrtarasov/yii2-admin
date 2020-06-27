<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 27.06.20 21:47:13
 */

declare(strict_types = 1);
namespace dicr\admin\models;

use dicr\file\StoreFile;
use dicr\helper\Html;
use dicr\validate\ValidateException;
use Yii;
use yii\base\Model;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * Базовая абстрактная форма.
 *
 * @property-read array|string|null $fromEmail адрес отправителя
 *
 * @property null|string|array $managerEmail
 * @property null|string $managerSubject
 * @property string[] $managerData
 * @property null|string $managerText
 * @property \dicr\file\StoreFile[]|null|\yii\web\UploadedFile[] $managerFiles
 * @property null|\yii\mail\MessageInterface $managerMessage
 *
 * @property null|string|array $userEmail
 * @property null|string $userSubject
 * @property null|\yii\mail\MessageInterface $userMessage
 * @property null|string $userText
 * @property \dicr\file\StoreFile[]|null|\yii\web\UploadedFile[] $userFiles
 *
 * @noinspection PhpUnused
 */
abstract class AbstractForm extends Model
{
    /**
     * Поле от кого.
     *
     * @return array|string|null
     */
    protected function getFromEmail()
    {
        return Yii::$app->params['email']['from'] ?? null;
    }

    /**
     * E-Mail менеджера.
     *
     * @return array|string|null
     */
    protected function getManagerEmail()
    {
        return Yii::$app->params['email']['manager'] ?? null;
    }

    /**
     * Тема сообщения менеджеру.
     *
     * @return string|null
     */
    protected function getManagerSubject()
    {
        return null;
    }

    /**
     * Данные сообщения менеджеру.
     *
     * @return string[]
     */
    protected function getManagerData()
    {
        $data = [];

        foreach ($this->attributes as $attribute => $value) {
            $data[Html::esc($this->getAttributeLabel($attribute))] = Html::esc($value);
        }

        return $data;
    }

    /**
     * Текст сообщения менеджеру.
     *
     * @return string|null
     */
    protected function getManagerText()
    {
        $data = $this->getManagerData();

        return empty($data) ? null : Yii::$app->view->render('@app/mail/table', [
            'data' => $data
        ]);
    }

    /**
     * Файлы в сообщение менеджеру.
     *
     * @return \yii\web\UploadedFile[]|\dicr\file\StoreFile[]|null
     */
    protected function getManagerFiles()
    {
        return null;
    }

    /**
     * Сообщение менеджеру.
     *
     * @return \yii\mail\MessageInterface|null
     * @noinspection DuplicatedCode
     */
    protected function getManagerMessage()
    {
        $text = $this->managerText;
        $files = $this->managerFiles;

        if (empty($this->managerEmail) || empty($this->managerSubject) || (empty($text) && empty($files))) {
            return null;
        }

        $message = Yii::$app->mailer->compose()
            ->setTo($this->managerEmail)
            ->setSubject($this->managerSubject)
            ->setCharset(Yii::$app->charset);

        if (! empty($this->fromEmail)) {
            $message->setFrom($this->fromEmail);
        }

        if (! empty($text)) {
            $message->setHtmlBody(Yii::$app->view->render('@app/mail/admin', [
                'message' => $message,
                'content' => $text
            ]));
        }

        if (! empty($files)) {
            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $message->attach($file->tempName, [
                        'fileName' => $file->name
                    ]);
                } elseif ($file instanceof StoreFile) {
                    $message->attach($file->absolutePath, [
                        'fileName' => $file->name
                    ]);
                }
            }
        }

        return $message;
    }

    /**
     * E-Mail пользователя.
     *
     * @return array|string|null
     */
    protected function getUserEmail()
    {
        return null;
    }

    /**
     * Заголовок сообщения пользователю.
     *
     * @return string|null
     */
    protected function getUserSubject()
    {
        return null;
    }

    /**
     * Текст сообщения пользователю.
     *
     * @return string|null
     */
    protected function getUserText()
    {
        return null;
    }

    /**
     * Файлы для сообщения пользователю.
     *
     * @return \yii\web\UploadedFile[]|\dicr\file\StoreFile[]|null
     */
    protected function getUserFiles()
    {
        return null;
    }

    /**
     * Сообщение пользователю.
     *
     * @return \yii\mail\MessageInterface|null
     * @noinspection DuplicatedCode
     */
    protected function getUserMessage()
    {
        $text = $this->getUserText();
        $files = $this->getUserFiles();

        if (empty($this->userEmail) || empty($this->userSubject) || (empty($text) && empty($files))) {
            return null;
        }

        $message = Yii::$app->mailer->compose()
            ->setTo($this->userEmail)
            ->setSubject($this->userSubject)
            ->setCharset(Yii::$app->charset);

        if (! empty($this->fromEmail)) {
            $message->setFrom($this->fromEmail);
        }

        if (! empty($text)) {
            $message->setHtmlBody(Yii::$app->view->render('@app/mail/user', [
                'message' => $message,
                'content' => $text
            ]));
        }

        if (! empty($files)) {
            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $message->attach($file->tempName, [
                        'fileName' => $file->name
                    ]);
                } elseif ($file instanceof StoreFile) {
                    $message->attach($file->absolutePath, [
                        'fileName' => $file->name
                    ]);
                }
            }
        }

        return $message;
    }

    /**
     * Обработка формы.
     *
     * @return bool
     * @throws \dicr\validate\ValidateException
     * @throws \yii\web\ServerErrorHttpException
     */
    public function process()
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        $managerMessage = $this->getManagerMessage();
        if (! empty($managerMessage) && ! $managerMessage->send()) {
            throw new ServerErrorHttpException('Ошибка отправки сообщения менеджеру');
        }

        $userMessage = $this->getUserMessage();
        if (! empty($userMessage) && ! $userMessage->send()) {
            throw new ServerErrorHttpException('Ошибка отправки сообщения пользователю');
        }

        return true;
    }
}
