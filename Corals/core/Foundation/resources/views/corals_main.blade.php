<script type="text/javascript">
    if (typeof CKEDITOR !== "undefined") {
        CKEDITOR.config.language = '{{ app()->getLocale() }}'
    }

    $(".select2-ajax").each(function () {
        var element = $(this);

        let parent = $(this).data('select2_parent');

        element.select2({
            dropdownParent: parent ? $(parent) : $('body'),
            ajax: {
                url: '{{ url('utilities/select2') }}',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        query: params.term, // search term
                        columns: $(this).data('columns'),
                        textColumns: $(this).data('text_columns'),
                        model: $(this).data('model'),
                        where: $(this).data('where'),
                        orWhere: $(this).data('or_where'),
                        resultMapper: $(this).data('result_mapper'),
                        join: $(this).data('join'),
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            allowClear: true
        });

        var selected = element.data('selected');

        if (selected.length) {
            $.ajax({
                url: '{{ url('utilities/select2') }}',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                delay: 250,
                data: {
                    selected: selected,
                    columns: element.data('columns'),
                    textColumns: element.data('text_columns'),
                    model: element.data('model'),
                    where: element.data('where'),
                    orWhere: element.data('or_where'),
                    resultMapper: element.data('result_mapper'),
                    join: element.data('join'),
                },
                success: function (data, textStatus, jqXHR) {
                    // create the option and append to Select2
                    for (var index in data) {
                        if (data.hasOwnProperty(index)) {
                            var selection = data[index];
                            var option = new Option(selection.text, selection.id, true, true);
                            element.append(option).trigger('change');
                        }
                    }
                }
            });
        }
    })
</script>
