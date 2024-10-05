filterable_gallery.element = jQuery(`.filterable-gallery`)
filterable_gallery.controls = filterable_gallery.element.find(`ul`)
filterable_gallery.items = filterable_gallery.element.find(`.eael-filter-gallery`)
filterable_gallery.load_more_btn = filterable_gallery.element.find(`.eael-gallery-load-more`)

function filterable_gallery_collect_filters() {
    filterable_gallery.filters = [`All`]
    for (let item of filterable_gallery.json) {
        if (0 > filterable_gallery.filters.indexOf(item.filter)) {
            filterable_gallery.filters.push(item.filter)
        }
    }
}

function filterable_gallery_write_filters() {
    const filters_html = filterable_gallery.filters.map(filter => {
        return `<li class="control">${filter}</li>`
    }).join(``)

    filterable_gallery.controls
        .html(filters_html)
        .find(`li`)
        .click(e => {
            filterable_gallery_apply_filter(e.target.innerHTML)
        })
}

function filterable_gallery_apply_filter(filter) {
    filterable_gallery.controls.find(`li`).removeClass(`active`)
    filterable_gallery.controls.find(`li:contains("${filter}")`).addClass(`active`)
    if (`All` !== filter) {
        filterable_gallery.items.find(`[data-filter]`).not((`[data-filter="${filter}"]`)).hide()
        filterable_gallery.element.find(`[data-filter="${filter}"]`).fadeIn()
    } else filterable_gallery.items.find(`[data-filter]`).fadeIn()
}

function filterable_gallery_lazy_load(index) {
    if (!filterable_gallery.json[index]) return false
    if (index == filterable_gallery.limit_lazy) return false

    if (0 == index) filterable_gallery.items.html(``)

    const item = filterable_gallery.json[index]
    filterable_gallery.items.append(`
        <div class="eael-filterable-gallery-item-wrap" data-filter="${item.filter}">
            <div class="eael-gallery-grid-item">
                <a class="eael-magnific-link eael-magnific-link-clone media-content-wrap">
                    <div class="gallery-item-thumbnail-wrap">
                        <img src="${item.src}" class="gallery-item-thumbnail" />
                    </div>
                </a>
            </div>
        </div >
    `)

    filterable_gallery_apply_filter(filterable_gallery_get_active_filter())

    setTimeout(() => {
        filterable_gallery_lazy_load(index + 1)
    }, filterable_gallery.interval_ms)
}

function filterable_gallery_get_active_filter() {
    return filterable_gallery.controls.find(`li.active`).html()
}

filterable_gallery.load_more_btn.click(e => {
    filterable_gallery.limit_lazy = filterable_gallery.json.length
    filterable_gallery_lazy_load(0)
})

filterable_gallery_collect_filters()
filterable_gallery_write_filters()
filterable_gallery_apply_filter(`All`)
filterable_gallery_lazy_load(0)