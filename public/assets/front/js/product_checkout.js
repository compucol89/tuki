"use strict";
//payment gateway start
$('select[name="gateway"]').on('change', function () {
  let value = $(this).val();
  let dataType = parseInt(value);

  if (isNaN(dataType)) {
    if ($('.offline-gateway-info').hasClass('d-block')) {
      $('.offline-gateway-info').removeClass('d-block');
    }

    // hide offline gateway informations
    $('.offline-gateway-info').addClass('d-none');
  } else {
    // hide offline gateway informations
    $('.offline-gateway-info').addClass('d-none');

    // show particular offline gateway informations
    $('#offline-gateway-' + value).removeClass('d-none');
  }
});

//payment gateway end

//calucate shipping method start
$('input[name="shipping_method"]').on('change', function () {
  var charge = $(this).attr('data-id');
  let s_charge = parseInt(charge);
  let cart_total = parseInt($('.cart_total').html());
  if ($('.shop_discount').length > 0) {
    var shop_discount = parseInt($('.shop_discount').html());
  } else {
    var shop_discount = 0;
  }

  var grand_total = (s_charge + cart_total) - shop_discount;
  $('.shipping_cost').html(s_charge);
  $('.grand_total').html(grand_total);
});
//calucate shipping method end

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$('#coupon-code').on('keypress', function (event) {
  if (event.key === "Enter") {
    event.preventDefault();
    var coupon_code = $("#coupon-code").val();
    var shipping_cost = parseInt($('.shipping_cost').html());

    $.ajax({
      type: 'POST',
      url: coupon_url,
      data: {
        coupon_code: coupon_code,
        shipping_cost: shipping_cost,
      },
      success: function (data) {
        $("#couponReload").load(location.href + " #couponReload");
        $('.shipping_cost').html(shipping_cost);

        toastr.options = {
          "closeButton": true,
          "progressBar": true,
          "timeOut": 10000,
          "extendedTimeOut": 10000,
          "positionClass": "toast-top-right",
        }
        $('#coupon-code').val('');
        if (data.status == 'error') {
          toastr.error(data.message);
        } else if (data.status == 'success') {
          toastr.success(data.message);
        }
      }
    });
  }
});

$("body").on('click', '.base-btn2', function (e) {
  e.preventDefault();
  var coupon_code = $("#coupon-code").val();
  var shipping_cost = parseInt($('.shipping_cost').html());

  $.ajax({
    type: 'POST',
    url: coupon_url,
    data: {
      coupon_code: coupon_code,
      shipping_cost: shipping_cost,
    },
    success: function (data) {
      $("#couponReload").load(location.href + " #couponReload");
      $('.shipping_cost').html(shipping_cost);

      toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "timeOut": 10000,
        "extendedTimeOut": 10000,
        "positionClass": "toast-top-right",
      }
      $('#coupon-code').val('');
      if (data.status == 'error') {
        toastr.error(data.message);
      } else if (data.status == 'success') {
        toastr.success(data.message);
      }
    }
  });
});
