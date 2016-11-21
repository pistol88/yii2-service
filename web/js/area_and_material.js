if (typeof usesgraphcrt == "undefined" || !usesgraphcrt) {
    var usesgraphcrt = {};
}

usesgraphcrt.calculate = {
        init: function () {
            $calculateServiceModal = $('#calculate-service');
            $calculateServiceForm = $('.calculate-service-data-form');
            $submitButton = $('.put-calculate-service-btn');
            $serviceMaterialInput = $calculateServiceForm.find('.calculate-service-material');
            $serviceWidthInput = $calculateServiceForm.find('.calculate-service-width');
            $serviceHeightInput = $calculateServiceForm.find('.calculate-service-height');
            $customServiceForm = $calculateServiceForm.find('#add-custom-service-form');
            $calculateServicePrice = $calculateServiceForm.find('.calculate-service-price');

            $(document).find('#calculate-service').on('keypress', function() {
                if ((event.keyCode == 13) && ($(this).find('.put-calculate-service-btn').prop('disabled')  != 'disabled')) {
                    $(this).find('.put-calculate-service-btn').click();
                }
            });

            $submitButton.on('click', function (e) {
                e.preventDefault();
                $customServiceForm.submit();
                $serviceWidthInput.val('');
                $serviceHeightInput.val('');
                $calculateServicePrice.text('');
            });

            $serviceMaterialInput.on('change',function () {
                usesgraphcrt.calculate.setPrice($serviceMaterialInput.val(),$serviceMaterialInput.find(':selected').text(),$serviceWidthInput.val(),$serviceHeightInput.val());
            });

            $calculateServiceForm.find('input').keyup(function () {
                usesgraphcrt.calculate.setPrice($serviceMaterialInput.val(),$serviceMaterialInput.find(':selected').text(),$serviceWidthInput.val(),$serviceHeightInput.val());
            });

        },
        setPrice: function (materialPrice, materialName,width,heigth) {
            if (materialPrice && width && heigth) {
                var calculateServiceName = $customServiceForm.data('service-name');
                price = Math.round(materialPrice*width.replace(/,/g, ".")*heigth.replace(/,/g, "."));
                if (price) {
                $calculateServicePrice.text('Итоговая цена: '+price+'р.');    
                }
                name = calculateServiceName + ' ('+materialName+ ';' +width+ 'x'+heigth+')';
                $customServiceForm.find('#customservice-name').val(name);
                $customServiceForm.find('#customservice-price').val(price);
                $submitButton.removeAttr('disabled');
            }
        }

    };

$( document ).ajaxComplete(function( event, xhr, settings ) {
    if (settings.url.indexOf("get-calculate-service-form-ajax") !== -1) {
        usesgraphcrt.calculate.init();
    }
});
