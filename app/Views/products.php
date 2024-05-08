<?= $this->extend('layouts/main'); ?>

<?= $this->section('results'); ?>

<?= $paginator; ?>

<div class="card card-body panel panel-default">
    <form method="post" action="/search-products">
        <input type="text" class="search-input" name="text">
        <button type="submit" class="search-submit">Search</button>
    </form>
    <br/>
    <br/>
    <table>
        <tr>
            <th class="col-xs-7">Desc</th>
            <th class="col-xs-2">OE</th>
            <th class="col-xs-1">Action</th>
        </tr>
        <?php foreach ($products as $item) : ?>
            <tr>
                <td class="col-xs-7"><?= $item->desc ?></td>
                <td class="col-xs-2"><?= $item->OE ?></td>
                <td class="col-xs-1"><a href="/edit-product/<?= $item->id ?>">edit</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>


<style>
    .search-input {
        width: 74%;
    }

    .search-submit {
        width: 25%;
        float: right;
    }

    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    table {
        width: 100%;
    }
</style>

<?= $this->endSection(); ?>
