<?php

use dektrium\user\tests\_pages\ResendPage;
use dektrium\user\models\User;
use yii\helpers\Html;

$I = new TestGuy($scenario);
$I->wantTo('ensure that resending of confirmation tokens works');

$page = ResendPage::openBy($I);

$I->amGoingTo('try to resend token to nonexistent user');
$page->resend('foo@example.com');
$I->see('Email is invalid');

$I->amGoingTo('try to resend token to already confirmed user');
$page->resend('user@example.com');
$I->see('This account has already been confirmed');

$I->amGoingTo('try to resend token to unconfirmed user');
$I->seeInDatabase('user', ['confirmation_token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6']);
$page->resend('unconfirmed@example.com');
$I->see('Awesome, almost there! We need to confirm your email address');
$I->dontSeeInDatabase('user', ['confirmation_token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6']);
$user = $I->grabRecord(User::className(), ['username' => 'unconfirmed']);
$I->seeEmailIsSent();
$email = $I->getLastMessage();
$I->seeEmailHtmlContains(Html::encode($user->getConfirmationUrl()), $email);