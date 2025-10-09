export default class ImagesGrid {
    constructor() {
        this.init();
    }
    init() {
        const grid = document.querySelector('.block-image-text .left-container');

        if(grid) {
            grid.addEventListener('mouseenter', () => {
                grid.classList.add('open');
            });
        }
    
    }
}