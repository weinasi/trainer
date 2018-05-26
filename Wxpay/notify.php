<?php
/**
 * 微信异步通知处理
 * @author hupeng
 */
include 'init.php';
include 'example/notify.php';
$notify = new PayNotifyCallBack();
$notify->Handle(false);

