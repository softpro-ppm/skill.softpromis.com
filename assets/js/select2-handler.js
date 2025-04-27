// Common Select2 initialization and handling
const Select2Handler = {
    // Initialize Select2 with basic config
    init: function(selector, options = {}) {
        const defaultOptions = {
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select an option'
        };
        
        return $(selector).select2({
            ...defaultOptions,
            ...options
        });
    },

    // Load options into Select2
    loadOptions: function(selector, data, valueField = 'id', textField = 'text', defaultOption = true) {
        const $select = $(selector);
        $select.empty();
        
        if(defaultOption) {
            $select.append(new Option('Select an option', ''));
        }
        
        data.forEach(item => {
            const option = new Option(item[textField], item[valueField]);
            $select.append(option);
        });
        
        $select.trigger('change');
    },

    // Set selected value
    setSelectedValue: function(selector, value) {
        $(selector).val(value).trigger('change');
    },

    // Load data from server and populate Select2
    loadFromServer: function(selector, url, params = {}, options = {}) {
        $.ajax({
            url: url,
            type: 'POST',
            data: params,
            success: function(response) {
                if(response.status) {
                    Select2Handler.loadOptions(
                        selector,
                        response.data,
                        options.valueField || 'id',
                        options.textField || 'text',
                        options.defaultOption !== false
                    );
                    
                    if(options.selectedValue) {
                        Select2Handler.setSelectedValue(selector, options.selectedValue);
                    }
                } else {
                    toastr.error('Error loading data');
                }
            },
            error: function() {
                toastr.error('Error loading data from server');
            }
        });
    },

    // Reset Select2
    reset: function(selector) {
        $(selector).val(null).trigger('change');
    }
}; 