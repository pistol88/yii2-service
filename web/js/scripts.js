if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}
$('#service-ident').focus();

pistol88.service = {
    init: function() {
        if($(document).width() > 1100) {
            $('.arm-right-column').css({'height': screen.height-370, 'width': '300px', 'overflow-x': 'hidden', 'position': 'fixed', 'overflow': 'hidden'});
            $('.service-order').css({'height': screen.height-310, 'width': '315px', 'overflow-y': 'scroll'});
            
            $(window).on('scroll', function(){
                if($('body').scrollTop() > 210) {
                    $('.arm-right-column, .service-order').css({'top': '50px', 'height': screen.height-250});
                } else {
                    $('.arm-right-column, .service-order').css({'top': 'auto', 'height': screen.height-370});
                }
            });
        }
        
        $(document).on('change', '.service-choose-property', this.chooseProperty);
        
        $(document).on('chooseUserToOrder', function(event, id) { pistol88.service.getProperties(id); })
        
        $(document).on('submit', '#add-custom-service-form', this.customServiceToCart)
        
        $(document).on('blur', '.get-sessions-by-date', this.getSessions);
        
        $(document).on('submit', '.order-create-container form', function() {
            $('.order-create-container form').css('css', '0.5');
            //pistol88.service.clearServiceOrder();
        });
        
        $(document).on('blur', '#service-ident', function() {
           $($(this).data('field-selector')).val($(this).val());
           pistol88.service.searchClientByIdent($(this).val());
        });

        $(document).on('click', '.pistol88-cart-truncate-button', function() {
            $('.service-order-net .price, .pistol88-cart-buy-button').css('border', '2px solid #c0e2ff');
        });
        
        $(document).on('click', 'input.service-price', function() {
            $(this).select();
        });

        $(document).on('click', '.calculate-service-model', function() {
            var self = $(this),
            url = self.data('url');
            pistol88.service.getCalculatedService(url);
            $('.calculate-service-model').removeClass('active');
            $(self).addClass('active');
        });
        
        $(document).on('click', '.service-order-net .price, .pistol88-cart-buy-button', function() {
            $(this).css('border', '2px solid #3F5696').addClass('checked');
        });

        $(document).on('click', '.service-order-net .price', function(e) {
            $(this).css('border', '2px solid #3F5696').addClass('checked');
            if(e.target.tagName != 'INPUT' && e.target.tagName != 'input') {
                $(this).find('.pistol88-cart-buy-button').click();
            }
        });

        $(document).on('keypress', function(e) {
            if(e.which == 13) {
                if(e.target.tagName != 'TEXTAREA' && e.target.tagName != 'textarea' && e.target.tagName != 'INPUT' && e.target.tagName != 'input') {
                    if(parseInt($('.pistol88-cart-count').val()) == 0) {
                        //if(!confirm('Создать пустой заказ?')) {
                            return false;
                        //}
                    }
                    if ($('.order-create-container form').data('ajax') != true ) {
                        $('.order-create-container form').submit();
                    }
                }
            }
        });

//        $(document).on('blur', 'input.service-price', function(e) {
//            $(this).siblings('.pistol88-cart-buy-button').click();
//        });
        
        $(document).on('mouseenter','.service-prices-table td', this.renderCross);
        
        $(document).on('click', '.pistol88-cart-buy-button, .service-order-net .price', this.addToCart);
        
        $(document).on('mouseleave','.service-prices-table td',function () {
            $('.service-prices-table td').removeClass('hover');
        });
        
        $(document).on('click', '.service-order-net .category a, .service-category', this.getServicesByCategory);
        $(document).on('click', '.service-order-net a.back', this.getCategories);
        
        $('.service-worker-payment').on('change', this.setPayment);
    },
    searchClientByIdentUrl: null,
    propertyListUrl: null,
    clearServiceOrder : function() {
        $('.order-create-container form').css('css', '0.5');
        $('.service-order .pistol88-cart-truncate-button').click();
        $('.pistol88-cart').html('Корзина пуста');
        $('.pistol88-cart-count').html('0');
        $('.pistol88-cart-informer').find('.pistol88-cart-count').html('0');
        $('.pistol88-cart-informer').find('.pistol88-cart-price').find("span").html('0');
		$('.pistol88-cart-informer').find('.pistol88-cart-price').find("s").remove();
        $('.service-order-net .header .back').click();
        $('#service-ident').val('').focus().select();
        $('#order-is_assigment').val(0);
        $('#order-payment_type_id').val(1);
        $('.promo-code-enter input[type=text]').val('');
        $('.promo-code-discount').hide();
        $('.order-create-container form input[type=text], .order-create-container form textarea').val('');
        $('.order-create-container form').css('css', '1');
        
        $(document).trigger('clearServiceOrder', this);
    },
    searchClientByIdent: function(ident) {
        jQuery.post(pistol88.service.searchClientByIdentUrl, {ident: ident},
            function(json) {
                if(json.result == 'success') {
                    pistol88.createorder.chooseUser(json.client_id);
                }
                else {
                    console.log(json.errors);
                }

                return true;

            }, "json");
            
        return false;
    },
    chooseProperty: function() {
        var category_id = $(this).find('option:selected').attr('data-category');

        if($(this).val() && $('#service-ident').val() == '') {
            $('#service-ident').val($(this).val());
        }
        
        $('.service-category-'+category_id).click();
        
		$($('#service-ident').data('field-selector')).val($(this).val());
		
        return true;
    },
    getProperties: function(clientId) {
        var select = $('.service-choose-property');

        $(select).html('<option value="" selected=selected>Автомобиль...</option>');

        jQuery.get(pistol88.service.propertyListUrl, {clientId: clientId},
            function(json) {
                if(json.result == 'success') {
                    $(json.list).each(function(i, el) {
                        selected = '';
                        if(i == 0) {
                            selected = "selected";
                        }
                        $(select).html($(select).html()+'<option '+selected+' value="'+el.name+'" data-category="'+el.category_id+'">'+el.name+'</option>');
                    });
                    
                    $('.service-choose-property').change();
                }
                else {
                    console.log(json.errors);
                }

                $('.service-choose-property').val($('#service-ident').val());
				
				$($('#service-ident').data('field-selector')).val($('#service-ident').val());
				
                return true;

            }, "json");

        return false;
    },
    customServiceToCart: function() {
        var form = $(this);
        var data = $(form).serialize();
        data = data+'&ajax=1';

        jQuery.post($(form).attr('action'), data,
            function(json) {
                if(json.result == 'success') {
                    $('#custom-service').modal('hide');
                    $('#custom-service input[type=text]').val('');
                    pistol88.createorder.updateCart();
                }
                else {
                    console.log(json.errors);
                }

                return true;

            }, "json");

        return false;
    },
    getCalculatedService: function (url) {
        $('[data-role=CalculateServiceForm]').css({'opacity': '0.3'});
        $('[data-role=CalculateServiceForm]').load(url, function() {
            
            $('[data-role=CalculateServiceForm]').css({'opacity': '1'});
        });
    },
    callPrint: function (strid) {
        var prtContent = document.getElementById(strid);
        var WinPrint = window.open('','','left=50,top=50,width=800,height=640,toolbar=0,scrollbars=1,status=0');
        WinPrint.document.write('<div id="printcontent" class="contentpane">');
        WinPrint.document.write(prtContent.innerHTML);
        WinPrint.document.write('</div>');
        WinPrint.document.write('<style>#printcontent input, #printcontent button, #printcontent select, .rc-handle-container { display: none; } #printcontent .modal-dialog { display: none; }</style>');
        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print();
        WinPrint.close();
    },
    setPayment: function() {
        if($(this).prop('checked')) {
            $(this).parent('div').removeClass('payment_no').addClass('payment_yes');
            var url = $(this).attr('data-set-href');
        } else {
            $(this).parent('div').removeClass('payment_yes').addClass('payment_no');
            $(this).parent('div').find('p').remove();
            var url = $(this).attr('data-unset-href');
        }

        $.post(url, {'worker_id': $(this).data('worker-id'), 'session_id': $(this).data('session-id'), 'sum': $(this).data('sum')},
            function(answer) {
                json = answer;
            }, "json");

        return false;
    },
    getCategories: function() {
		$('.services').css('opacity', '0.3');
        $.post($(this).attr('href'), {},
            function(answer) {
				$('.services').css('opacity', '1');
                json = answer;
                $('.service-order-net').replaceWith(json.HtmlBlock);
            }, "json");

        return false;
    },
    getSessions: function() {
        console.log(2);
        var input = $(this);
        $.post($(this).attr('href'), {date: $(this).val()},
            function(answer) {
                json = answer;
                $(input).siblings('ul').replaceWith(json.HtmlList);
            }, "json");

        return false;
    },
    getServicesByCategory: function() {
        var childs = $(this).parent('.category').find('.childs');
        
        if($(childs).length) {
            $('.service-order-net .header').append($(this).data('title'));
            $('.service-order-net .categories').html($(childs).html());
        } else {
            $('.service-order-net').css('opacity', '0.3');
            $.post($(this).attr('href'), {id: $(this).data('id')},
                function(answer) {
                    $('.service-order-net').css('opacity', '1');
                    json = answer;
                    $('.service-order-net').replaceWith(json.HtmlBlock);
                }, "json");
        }

        return false;
    },
    renderCross: function () {
        console.log('renderCross');
        var tr = $(this).parent('tr');
        var Col = tr.find('td').index(this);

        tr.find('td').addClass('hover');
        $('.service-prices-table tr').find('td:eq(' + Col + ')').addClass('hover');
    },
    addToCart: function(e) {
        $('.service-order').css('opacity', '0.3');
        
        setTimeout(function() { $('.service-order').css('opacity', '1') }, 300);
        
        $(this).data('price', $(this).siblings('input').val());
        $(this).siblings('input').val($(this).siblings('input').data('base-price'));
        
        return true;

        var x = e.pageX;
        
        if(x) {
            var y = e.pageY;

            var cart_pos = $('.service-order h3 .pistol88-cart-count').offset();

            $('.pistol88-cart-informer').css('opacity','0.3');

            $('<div class="service_tocart_point"></div>')
                .appendTo($('body'))
                .css(
                    {
                        'position': 'absolute',
                        'display': 'block',
                        'margin-top': '-35px',
                        'z-index': '1500',
                        'left': x,
                        'top': y,
                        'opacity': '0.9'
                    })
                .animate(
                    {
                        'top': cart_pos.top+52,
                        'left': cart_pos.left,
                        'opacity': '0.5'
                    },
                    1100,
                    function() {
                        $('.pistol88-cart-informer').css('opacity','1');jQuery(this).remove();
                    });
        }
        return true;
    }
}

pistol88.service.init();
