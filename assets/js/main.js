$(function(){
  $("#check-email-form").submit(function(e){
    $("#total").html('');
    $("#valid").html('');
    $("#syntax").html('');
    $("#invalid").html('');
    $("#unknown").html('');
    $("#duplicate").html('');
    $(".store").attr("disabled",'true');
    $(".download").attr("disabled",'true');
    e.preventDefault();
    $(".tbody-data").html('');
    temails1 = $("#email").val();
    temails1 =$.trim(temails1);
    temails1 = temails1.replace(/ /g,'')
    temails1 = temails1.split("\n");
    var tcount1 = temails1.length;
    var email_arr1 = [];
    for(var i = 0; i < tcount1 ; i++){
      var a = 'arr'+i;
      var r1 = temails1.indexOf(temails1[i])+1;
      var r2 = $.trim(temails1[i]);
      a = [r1, r2]
      email_arr1.push(a);
    }

    temails1 = temails1.filter(Boolean);
    if(temails1 != ''){
      $('.start').css('display','block');
      $('.end').css('display','none');
    }
    $('.end_store').css('display','none');
    $('.start_store').css('display','none');

    email_arr1 = email_arr1.map(JSON.stringify).reverse() // convert to JSON string the array content, then reverse it (to check from end to begining)
  .filter(function(item, index, arr){ return arr.indexOf(item, index + 1) === -1; }) // check if there is any occurence of the item in whole array
  .reverse().map(JSON.parse) // revert it to original state
    var count1 = email_arr1.length;

    var duplicate = tcount1 - count1;
    $("#total").html(tcount1);
    $("#duplicate").html(duplicate);
    var t = 0;
    var e_count = email_arr1.length;
    var token = '12345';
    for (var i = 0; i < e_count; i++) {
      var index = email_arr1[i][0];
      var email = email_arr1[i][1];
      if(email){
        $(".tbody-data").append("<tr><td>" + (index)+ "</td><td>" + email + "</td><td class='email-" + index +"'>Handling ... </td><td style='display:none' class='status-" + index +"'>server error </td></tr>");
        email = $.trim(email);
        if (email != '') {
          $.ajax({
            url: "app/handle.php",
            type: "post",
            data: {
              email: email,
              index: index,
              token: token
            },
          }).done(function(result){
                t++;
                var regex = /\b[^\s]*\d[^\s]*\b/g;
                var status = result.status.toString().replace(regex,'')
                status = status.replace('[]','')
                status = status.replace(/ \<[\s\S]*?\>/g, '');
                status = status.trim();
                $(".status-" + result.index).html(status+'\n');
            switch(result.code){
              case 0:
                $(".email-" + result.index).html('<span class="invalid badge badge-danger">Invalid</span>');
                break;
              case 1:
                $(".email-" + result.index).html('<span class="valid badge badge-success">Valid</span>');

                break;
              case 2:
                $(".email-" + result.index).html('<span style="background:#c3c3c3;" class="error badge badge-light">Unknown</span>');
                break;
              case 3:
                $(".email-" + result.index).html('<span class="syntax badge badge-warning">Syntax error</span>');
                break;
            }
            if(t == e_count && result){
              $(".store").removeAttr("disabled");
              $(".download").removeAttr("disabled");
              $('.start').css('display','none');
              $('.end').css('display','block');
              var numItems = $('.valid').length;
              var numItems2 = $('.invalid').length;
              var numItems3 = $('.error').length;
              var numItems4 = $('.syntax').length;
              var numItems5 = $("#duplicate").html();
              setTimeout(function(){ $('.end').css('display','none'); }, 2000);
              $("#valid").html(numItems);
              $("#syntax").html(numItems4);
              $("#invalid").html(numItems2);
              $("#unknown").html(numItems3);
              var ctx = document.getElementById('myChart').getContext('2d');
              var myChart = new Chart(ctx, {
                  type: 'pie',
                  data: {
                      labels: ['Valid', 'Invalid', 'syntax error', 'Unknown','duplicate'],
                      datasets: [{
                          label: '# of Votes',
                          data: [numItems, numItems2, numItems4, numItems3, numItems5],
                          backgroundColor: [
                              '#28a745',
                              '#ff2d42',
                              '#ffc107',
                              '#c3c3c3',
                              '#4a4949'

                          ],
                          borderColor: [
                              '#28a745',
                              '#ff2d42',
                              '#ffc107',
                              '#c3c3c3',
                              '#4a4949'

                          ],
                          borderWidth: 1
                      }]
                  },
                  options: {
                      scales: {
                          yAxes: [{
                              ticks: {
                                  beginAtZero: true
                              }
                          }]
                      }
                  }
              });
            }
          })

        }
      }else{
        t++;
      }
    }


    return false;
  })

});
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Valid', 'Invalid', 'syntax error', 'Unknown','duplicate'],
        datasets: [{
            label: '# of Votes',
            data: [0, 0, 0, 0,0],
            backgroundColor: [
              '#28a745',
              '#ff2d42',
              '#ffc107',
              '#c3c3c3',
              '#4a4949'

            ],
          borderColor: [
            '#28a745',
            '#ff2d42',
            '#ffc107',
            '#c3c3c3',
            '#4a4949'

            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
$('.valid-btn').click(function(e){
  $(this).parents('.li').remove();
  $('.invalid').parents('td').parents('tr').css('display','none');
  $('.error').parents('td').parents('tr').css('display','none');
  $('.syntax').parents('td').parents('tr').css('display','none');
  $('.valid').parents('td').parents('tr').css('display','table-row');
})
$('.invalid-btn').click(function(e){
  $(this).parents('.li').remove();
  $('.invalid').parents('td').parents('tr').css('display','table-row');
  $('.error').parents('td').parents('tr').css('display','table-row');
  $('.syntax').parents('td').parents('tr').css('display','table-row');
  $('.valid').parents('td').parents('tr').css('display','none');
})
$('.all-btn').click(function(e){
  $(this).parents('.li').remove();
  $('.invalid').parents('td').parents('tr').css('display','table-row');
  $('.error').parents('td').parents('tr').css('display','table-row');
  $('.syntax').parents('td').parents('tr').css('display','table-row');
  $('.valid').parents('td').parents('tr').css('display','table-row');
})
