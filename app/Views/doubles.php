<?= $this->extend('layouts/main'); ?>

<?= $this->section('results'); ?>

<div class="container">
    <div class="card card-body panel panel-default">
        <div class="panel-body">
            <table>
                <tr>
                    <th class="col-xs-7">Desc</th>
                    <th class="col-xs-2">OE</th>
                    <th class="col-xs-1">Price</th>
                    <th class="col-xs-2">New price</th>
                    <th class="col-xs-2">Average</th>
                    <th class="col-xs-2">Url</th>
                </tr>
                <?php foreach ($items as $item) : ?>
                    <tr>
                        <td class="col-xs-7"><?= $item->desc ?></td>
                        <td class="col-xs-2"><?= $item->OE ?></td>
                        <td class="col-xs-1"><?= $item->price ?></td>
                        <td class="col-xs-2"><?= $item->newPrice ?></td>
                        <td class="col-xs-2"><?= $item->average ?></td>
                        <td class="col-xs-2"><a href="<?= $item->url ?>" target="_blank">Link</a></td>
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
