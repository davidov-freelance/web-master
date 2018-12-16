<div class="gross-buttons-block">
    {{Html::link( backend_url('shows'), 'Shows', ['class' => 'btn btn-default pull-right'])}}
    <label class="btn btn-warning pull-right">
        <span>Import</span>
        <input class="hidden import-csv" type="file" accept="text/csv">
    </label>
    {{Html::link( backend_url('shows/gross/add/' . $show->id), 'Add gross', ['class' => 'btn btn-success pull-right'])}}
</div>