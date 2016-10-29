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
            $calculateServiceName = $customServiceForm.find('#customservice-name').val();

            $submitButton.on('click', function (e) {
                e.preventDefault();
                $customServiceForm.submit();
                $calculateServiceModal.modal('hide');
                $calculateServiceForm.remove();
            });
            
            $serviceMaterialInput.on('change',function () {
                usesgraphcrt.calculate.setPrice($serviceMaterialInput.val(),$serviceMaterialInput.find(':selected').text(),$serviceWidthInput.val(),$serviceHeightInput.val());
            });
            
            $calculateServiceForm.find('input').keyup(function () {
                usesgraphcrt.calculate.setPrice($serviceMaterialInput.val(),$serviceMaterialInput.find(':selected').text(),$serviceWidthInput.val(),$serviceHeightInput.val());
            });

        },
        setPrice: function (material,name,width,heigth) {
            if (material && width && heigth) {
                price = material*width*heigth;
                $calculateServicePrice.text('Итоговая цена: '+price+'р.');
                name = $calculateServiceName + ' ('+name+ ';' +width+ 'x'+heigth+')';
                $customServiceForm.find('#customservice-name').val(name);
                $customServiceForm.find('#customservice-price').val(price);
                $submitButton.removeAttr('disabled');
            }
        }
        
    };

$( document ).ajaxComplete(function( event, xhr, settings ) {
    console.log(settings.url);
    if (settings.url.indexOf("get-calculate-service-form-ajax") !== -1) {
        usesgraphcrt.calculate.init();
    }
});