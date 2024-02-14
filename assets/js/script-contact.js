document.addEventListener('DOMContentLoaded', function() {
  var buttonAreas = document.querySelectorAll('.btn-area');

  buttonAreas.forEach(function(buttonArea) {
    var content = buttonArea.nextElementSibling;
    content.style.display = 'none';

    buttonArea.addEventListener('click', function(e) {
      e.preventDefault();
      if (content.style.display === 'none') {
        content.style.display = 'block';
        buttonArea.querySelector('.btn').textContent = '閉じる';
        buttonArea.querySelector('.btn').classList.add('close');
        buttonArea.querySelector('.btn').classList.remove('boxopen');
      } else {
        content.style.display = 'none';
        buttonArea.querySelector('.btn').textContent = 'さらに詳しく';
        buttonArea.querySelector('.btn').classList.remove('close');
        buttonArea.querySelector('.btn').classList.add('boxopen');
      }
    });
  });
});