<?= $this->extend('layouts/main'); ?>

<?= $this->section('upload'); ?>

<div class="container">
    <div class="card card-body panel panel-default">
        <div class="panel-body">
            <h3>Настройка импорта</h3>
            <br/>

            <form method="post" action="/settings">
                <div class="row">
                    <div class="col-lg-3">OE</div>
                    <div class="col-lg-3">
                        <input type="text" name="import_oe" value="<?= $items['import_oe'] ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">Описание</div>
                    <div class="col-md-3">
                        <input type="text" name="import_desc" value="<?= $items['import_desc'] ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">Цена</div>
                    <div class="col-sm-3">
                        <input type="text" name="import_price" value="<?= $items['import_price'] ?>">
                    </div>
                </div>
                <br/>
                <input type="submit" value="Сохранить">
            </form>

            <br/>
            <br/>
            <a href="/">На главную</a>
        </div>
    </div>
</div>


<style>
    .row {
        margin-bottom: 4px;
    }
</style>

<?= $this->endSection(); ?>
