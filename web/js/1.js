$(function(){
  const modal      = $('#dishModal');
  const modalList  = $('#modalList tbody');
  const search     = $('#modalSearch');
  let idx          = $('#dishes-table tbody tr').length;
  let currentRow;

  function addRow(i) {
    $('#dishes-table tbody').append(`
      <tr data-index="${i}">
        <td>
          <input type="hidden" name="OrderDishForm[${i}][dish_id]">
          <input type="text" class="form-control dish-picker" readonly placeholder="Кликните для выбора">
        </td>
        <td>
          <input type="number" min="1" value="1" class="form-control"
                 name="OrderDishForm[${i}][count]">
        </td>
        <td><button type="button" class="btn btn-danger remove-row">&times;</button></td>
      </tr>`);
  }

  // Если пусто – создаём одну строку сразу
  if (idx === 0) addRow(idx++);

  // Открытие модалки и загрузка всех блюд
  $('#dishes-table').on('click', '.dish-picker', function(){
    currentRow = $(this).closest('tr');
    modal.modal('show');
    search.val('');
    loadDishes('');  // сразу получаем весь список
  });

  // Добавить/удалить строку
  $('#add-row').click(() => addRow(idx++));
  $('#dishes-table').on('click', '.remove-row', function(){
    $(this).closest('tr').remove();
  });

  // Функция загрузки блюд через AJAX
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

  // Фильтрация при вводе
  search.on('input', function(){
    loadDishes($(this).val());
  });

  // Выбор блюда
  modalList.on('click', 'tr', function(){
    const name = $(this).find('td').text();
    const id   = $(this).find('td').data('id');
    currentRow.find('input[type="text"]').val(name);
    currentRow.find('input[type="hidden"]').val(id);
    modal.modal('hide');
  });
});
