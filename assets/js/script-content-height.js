document.addEventListener('DOMContentLoaded', function () {
    function adjustTimelineItemHeight() {
        const items = document.querySelectorAll('.timeline-item');

        items.forEach((item, index) => {
            const nextItem = items[index + 1];

            // 弟要素が存在する場合、兄要素と弟要素の高さを比較して、短い方に合わせる
            if (nextItem) {
				const itemHeight = item.children[0].dataset.height
                const nextItemHeight = nextItem.offsetHeight;

				item.children[0].dataset.height = `${nextItemHeight}px`;
            }
        });
    }

    // ページ読み込み時に実行
    adjustTimelineItemHeight();

    // ウィンドウのサイズが変更されたときにも実行
    let resizeTimer;

    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(adjustTimelineItemHeight, 250);
    });
});
