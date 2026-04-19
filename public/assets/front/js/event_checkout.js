"use strict";
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

//coupon code script
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$('#coupon-code').on('keypress', function (event) {
  if (event.key === "Enter") {
    event.preventDefault();
    var coupon_code = $("#coupon-code").val();
    $.ajax({
      type: 'POST',
      url: url,
      data: {
        coupon_code: coupon_code,
      },
      success: function (data) {
        $("#coupon-code").val('');
        $("#couponReload").load(location.href + " #couponReload");
        if (data.status == 'success') {
          toastr['success'](data.message);
        } else {
          toastr['error'](data.message);
        }
      }
    });
  }
});

$(".base-btn").on('click', function (e) {
  e.preventDefault();
  var coupon_code = $("#coupon-code").val();
  $.ajax({
    type: 'POST',
    url: url,
    data: {
      coupon_code: coupon_code,
    },
    success: function (data) {
      $("#coupon-code").val('');
      $("#couponReload").load(location.href + " #couponReload");
      if (data.status == 'success') {
        toastr['success'](data.message);
      } else {
        toastr['error'](data.message);
      }
    }
  });
});

$('#coupon-code').on('submit', function (e) {
  e.preventDefault();
});
