// ページが読み込まれたときにも要素が見えているか確認するために初期実行
window.addEventListener('DOMContentLoaded', checkElementsVisibility);

window.addEventListener('scroll', checkElementsVisibility);

function checkElementsVisibility() {
  const slideinElements = document.querySelectorAll('.slide-in');
  const windowHeight = window.innerHeight;

  slideinElements.forEach(function(element, index) {
    const elemPos = element.getBoundingClientRect().top;
    const scroll = window.scrollY;

    if (scroll > elemPos - windowHeight || elemPos < windowHeight) {
      // アニメーションの遅延を設定
      setTimeout(function() {
        element.classList.add('scroll-in');
      }, index * 50); // 200ミリ秒ごとに遅延を増加させる
    }
  });
}