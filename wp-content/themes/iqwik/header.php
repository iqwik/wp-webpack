<?php $title = wp_get_document_title();?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title><?php echo $title;?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" href="<?php echo TEMPLATE_URL?>/style.css?ver=<?php echo BUNDLE_VERSION;?>" />
    <!-- META -->
    <meta property="og:locale" content="ru_RU" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $title;?>" />
    <meta property="og:url" content="<?php echo SITE_URL?>" />
    <meta property="og:site_name" content="<?php echo SITE_NAME?>" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo $title;?>" />
    <meta name="apple-mobile-web-app-title" content="<?php echo $title;?>" />
    <meta name="application-name" content="<?php echo $title;?>" />
    <?php wp_head(); ?>
</head>
<body>
<div class="wrapper">
