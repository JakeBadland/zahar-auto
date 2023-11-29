<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <div class="card card-body panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6">Всего товаров в базе:</div>
                <div class="col-xs-6"><?=$data['count']?></div>
            </div>
            <div class="row">
                <div class="col-xs-6">Последнее обновление:</div>
                <div class="col-xs-6"><?=$data['updated']?></div>
            </div>
            <div class="row">
                <div class="col-xs-6">Найдено дешевле:</div>
                <div class="col-xs-6"><?=$data['find']?></div>
            </div>
        </div>
    </div>

    <div><a href="/results">Показать результаты</a></div>
    <div><a href="/upload">Загрузить новый файл</a></div>
    <div><a href="/clear">Удалить все товары</a></div>
<!--    <div><a href="/uploads/datafile.csv">Сохранить загруженный файл</a></div>-->

</div>

<script>
    $(document).ready(function () {

    });
</script>

<?= $this->endSection(); ?>
