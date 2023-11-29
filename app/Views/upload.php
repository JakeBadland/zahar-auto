<?= $this->extend('layouts/main'); ?>

<?= $this->section('upload'); ?>

<div class="container">
    <div class="card card-body panel panel-default">
        <div class="panel-body">
            <form enctype="multipart/form-data" method="post" action="/upload">
                <input type="file" name="datafile" accept="text/csv">
                <br/>
                <input type="submit" value="Загрузить">
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
