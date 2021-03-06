<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 01.07.20 22:11:37
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
     * Возвращает текст сообщения менеджеру
     *
     * @return string|null
     */
    protected function getManagerText()
    {
        $data = $this->getManagerData();
        if (empty($data)) {
            return null;
        }

        $text = Yii::$app->view->render('@app/mail/table', [
            'data' => $data
        ]);

        return Yii::$app->view->render('@app/mail/admin', [
            'content' => $text
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
        $to = $this->getManagerEmail();
        if (empty($to)) {
            return null;
        }

        $subject = $this->getManagerSubject();
        if (empty($subject)) {
            return null;
        }

        $text = $this->getManagerText();
        $files = $this->getManagerFiles();
        if (empty($text) && empty($files)) {
            return null;
        }

        $message = Yii::$app->mailer->compose()
            ->setTo($to)
            ->setSubject($subject)
            ->setCharset(Yii::$app->charset);

        $from = $this->getFromEmail();
        if (! empty($from)) {
            $message->setFrom($from);
        }

        if (! empty($text)) {
            $message->setHtmlBody($text);
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
        $to = $this->getUserEmail();
        if (empty($to)) {
            return null;
        }

        $subject = $this->getUserSubject();
        if (empty($subject)) {
            return null;
        }

        $text = $this->getUserText();
        $files = $this->getUserFiles();
        if (empty($text) && empty($files)) {
            return null;
        }

        $message = Yii::$app->mailer->compose()
            ->setTo($to)
            ->setSubject($subject)
            ->setCharset(Yii::$app->charset);

        $from = $this->getFromEmail();
        if (! empty($from)) {
            $message->setFrom($from);
        }

        if (! empty($text)) {
            $message->setHtmlBody($text);
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
