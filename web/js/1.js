$(function(){
  const modal      = $('#dishModal');
  const modalList  = $('#modalList tbody');
  const search     = $('#modalSearch');
  let idx          = $('#dishes-table tbody tr').length;
  let currentRow;

  // Функция добавления строки
  function addRow(i) {
    $('#dishes-table tbody').append(`
      <tr data-index="${i}">
        <td>
          <input type="hidden" name="OrderDish[${i}][dish_id]" value="">
          <input type="text" class="form-control dish-picker" 
                 name="OrderDish[${i}][dish_name]"
                 readonly placeholder="Кликните для выбора">
        </td>
        <td>
          <input type="number" min="1" value="1" class="form-control"
                 name="OrderDish[${i}][count]">
        </td>
        <td><button type="button" class="btn btn-danger remove-row">&times;</button></td>
      </tr>`);
  }

  // Если таблица пустая — добавляем одну строку по умолчанию
  if (idx === 0) addRow(idx++);

  // Добавление строки
  $('#add-row').click(() => addRow(idx++));

  // Удаление строки
  $('#dishes-table').on('click', '.remove-row', function(){
    $(this).closest('tr').remove();
  });

  // Клик по полю блюда — открыть модалку
  $('#dishes-table').on('click', '.dish-picker', function(){
    currentRow = $(this).closest('tr');
    modal.modal('show');
    search.val('');
    loadDishes('');
  });

  // Загрузка списка блюд через AJAX
  function loadDishes(q) {
    $.getJSON('dish-list', { q }, function(data){
      modalList.empty();
      data.forEach(item => {
        $('<tr>').append(
          $('<td>').text(item.label).data('id', item.id)
        ).appendTo(modalList);
      });
    });
  }

  // Поиск по блюдам в модалке
  search.on('input', function(){
    loadDishes($(this).val());
  });

  // Выбор блюда из списка
  modalList.on('click', 'tr', function(){
    const name = $(this).find('td').text();
    const id   = $(this).find('td').data('id');
    currentRow.find('input[name$="[dish_name]"]').val(name);
    currentRow.find('input[name$="[dish_id]"]').val(id);
    modal.modal('hide');
  });
});
