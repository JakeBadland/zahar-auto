<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <div class="card card-body panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">Время сейчас:</div>
                <div class="col-xs-6"><?= date('d-m-Y H:i:s'); ?></div>
            </div>
            <div class="row">
                <div class="col-xs-6">Последнее обновление базы:</div>
                <div class="col-xs-6"><?=$data['updated_data']?></div>
            </div>
            <div class="row">
                <div class="col-xs-6">Всего товаров в базе:</div>
                <div class="col-xs-6"><?=$data['count']?></div>
            </div>
            <div class="row">
                <div class="col-xs-6">Товаров с ошибками:</div>
                <div class="col-xs-6"><?=$data['errors_count']?></div>
            </div>
            <div class="row">
                <div class="col-xs-6">Обновлено товаров за сутки:</div>
                <div class="col-xs-6"><?=$data['updated_products']?></div>
            </div>
            <div class="row">
                <div class="col-xs-6">Найдено дешевле:</div>
                <div class="col-xs-6"><?=$data['find']?></div>
            </div>
            <div class="row">
                <div class="col-xs-6">Последний скан:</div>
                <div class="col-xs-6">
                    <?php if ($data['last_parsed']) : ?>
                        <?=$data['last_parsed']->OE?>
                    <?=$data['last_parsed']->desc?>
                    (<?=$data['last_parsed']->parsed_at?>)
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <div><a href="/results">Показать результаты</a></div>
    <div><a href="/error-products">Показать товары с ошибками</a></div>
    <div><a href="/settings">Настрока импорта</a></div>
    <div><a href="/upload">Загрузить новый файл</a></div>
    <div><a href="/export">Результат в файле</a></div>
    <div><a href="/doubles">Анализ на дубликаты</a></div>
    <div><a href="/list/1">Редактировать товары</a></div>
<!--    <div><a href="/clear">Удалить все товары</a></div>-->
<!--    <div><a href="/uploads/datafile.csv">Сохранить загруженный файл</a></div>-->

</div>

<script>
    $(document).ready(function () {

    });
</script>

<?= $this->endSection(); ?>
