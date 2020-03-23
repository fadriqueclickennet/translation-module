@extends('layouts.master')

@section('content-header')
    <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">  {{ trans('translation::translations.title.translations') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('dashboard.index') }}"><i
                                class="fas fa-tachometer-alt mr-1"></i>{{ trans('core::core.breadcrumb.home') }}</a></li>
                        <li class="breadcrumb-item active">  {{ trans('translation::translations.title.translations') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row justify-content-end">
                <div class="col-auto">
                    <a href="" class="btn btn-primary jsClearTranslationCache mr-2"><i class="fas fa-sync-alt mr-1"></i> {{ trans('translation::translations.Clear translation cache') }}</a>
                    <div class="btn-group float-right" style="margin: 0 15px 15px 0;">
                        <a href="{{ route('admin.translation.translation.export') }}" class="btn btn-info">{{ trans('translation::translations.Export') }}</a>
                        <a data-toggle="modal" data-target="#ImportModal" class="btn btn-info" style="color: white;">{{ trans('translation::translations.Import') }}</a>
                    </div>
                </div>
            </div>
            <div class="card card-primary card-outline">
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="data-table table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Key</th>
                            <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                                <th>{{ $locale }}</th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($translations)): ?>
                        <?php foreach ($translations->all() as $key => $translationGroup): ?>
                        <tr>
                            <td>{{ $key }}</td>
                            <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                                <td style="position:relative;">
                                    <a class="translation" data-pk="{{ $locale }}__-__{{ $key }}">{{ is_array(array_get($translationGroup, $locale, null)) ?: array_get($translationGroup, $locale, null) }}</a>
                                    <a href="" style="position: absolute; right: 5px;" class="openRevisionModal"
                                       data-pk="{{ $locale }}__-__{{ $key }}"><i class="fas fa-search-plus"></i></a>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Key</th>
                            <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                            <th>{{ $locale }}</th>
                            <?php endforeach; ?>
                        </tr>
                        </tfoot>
                    </table>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" tabindex="-1" role="dialog" id="ImportModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ trans('translation::translations.Import csv translations file') }}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> 
                </div>
                {!! Form::open(['route' => 'admin.translation.translation.import', 'method' => 'post', 'files' => true]) !!}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">{{ trans('translation::translations.select CSV file') }}</label>
                        <input type="file" name="file" class="{{ $errors->has('file') ? 'is-invalid' : '' }}">
                        {!! $errors->first('file', '<div class="invalid-feedback">:message</div>') !!}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('core::core.button.cancel') }}</button>
                    <button class="btn btn-primary" type="submit">{{ trans('translation::translations.Import') }}</button>
                </div>
                {!! Form::close() !!}
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="modal-translation-history" tabindex="-1" role="dialog" aria-labelledby="modal-translation-history" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                        <h4 class="modal-title" id="delete-confirmation-title">Historial en: keyname</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> 
                    </div>
                <div class="modal-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ trans('translation::translations.history') }}</th>
                                <th width="200px">{{ trans('translation::translations.author') }}</th>
                                <th width="110px">{{ trans('translation::translations.event') }}</th>
                                <th width="130px">{{ trans('translation::translations.time') }}</th>
                                <th width="10px">{{ trans('core::core.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="history"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn btn-default" data-dismiss="modal">{{ trans('core::core.button.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

@stop

@push('js-stack')
    <?php if ($errors->has('file')): ?>
    <script>
        $( document ).ready(function() {
            $('#ImportModal').modal('show');
        });
    </script>
    <?php endif; ?>
    <script>
        $( document ).ready(function() {
            $('.openRevisionModal').on('click', function (event) {
                event.preventDefault();
                var modal = $('#modal-translation-history');
                var splitKey = $(this).data('pk').split("__-__");
                var locale = splitKey[0];
                var key = splitKey[1];
                var title = modal.find('.modal-title').text().replace('keyname', key);
                modal.find('.modal-title').text(title);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('api.translation.translations.revisions') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        locale: locale,
                        key: key
                    },
                    success: function(data) {
                        data.forEach(function(element) {
                            $('.history').append(element);
                        });
                        modal.modal('show');
                    }
                });
            });
            $('#modal-translation-history').on('hidden.bs.modal', function (event) {
                $('.history').empty();
            });
        });
        $( document ).ready(function() {
            $('.jsClearTranslationCache').on('click',function (event) {
                event.preventDefault();
                var $self = $(this);
                $self.find('i').toggleClass('fas-spinner');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('api.translation.translations.clearCache') }}',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function() {
                        $self.find('i').toggleClass('fas-spinner');
                    }
                });
            });
        });
        $(function() {
            $('a.translation').editable({
                url: function(params) {
                    var splitKey = params.pk.split("__-__");
                    var locale = splitKey[0];
                    var key = splitKey[1];
                    var value = params.value;

                    if (! locale || ! key) {
                        return false;
                    }

                    $.ajax({
                        url: '{{ route("api.translation.translations.update") }}',
                        method: 'POST',
                        data: {
                            locale: locale,
                            key: key,
                            value: value,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                        }
                    })
                },
                type: 'textarea',
                mode: 'inline',
                send: 'always', /* Always send, because we have no 'pk' which editable expects */
                inputclass: 'translation_input'
            });
        });
    </script>
    <?php $locale = locale(); ?>
    <script type="text/javascript">
        $(function () {
            $('.data-table').dataTable({
                "paginate": true,
                "lengthChange": true,
                "filter": true,
                "sort": true,
                "info": true,
                "autoWidth": true,
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@endpush
