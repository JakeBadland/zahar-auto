<?= $this->extend('layouts/main'); ?>

<?= $this->section('results'); ?>

<div class="container">
    <div class="card card-body panel panel-default">
        <div class="panel-body">
            <table>
                <tr>
                    <th class="col-xs-6">Desc</th>
                    <th class="col-xs-3">OE</th>
                    <th class="col-xs-3">Price</th>
                </tr>

                <?php foreach ($items as $item) : ?>
                    <tr>
                        <td class="col-xs-6"><?= $item->desc ?></td>
                        <td class="col-xs-3"><?= $item->OE ?></td>
                        <td class="col-xs-3"><?= $item->price ?></td>
                    </tr>
                <?php endforeach; ?>

            </table>
        </div>
    </div>
</div>

<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    table{
        width: 100%;
    }
</style>

<?= $this->endSection(); ?>
