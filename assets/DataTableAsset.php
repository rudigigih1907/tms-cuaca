<?php

namespace app\assets;

use yii\web\AssetBundle;

class DataTableAsset extends AssetBundle
{
    public $css = [
        'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css',
    ];

    public $js = [
        'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
        'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
