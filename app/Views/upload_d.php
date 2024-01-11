<?= $this->extend('layouts/main'); ?>

<?= $this->section('upload'); ?>

<div class="container">
    <div class="card card-body panel panel-default">
        <div class="panel-body">
            <h3>Анализ на дубликаты</h3>
            <br/>
            <form enctype="multipart/form-data" method="post" action="/doubles">
                <input type="file" name="testfile" accept="text/csv">
                <br/>
                <input type="submit" value="Загрузить">
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
