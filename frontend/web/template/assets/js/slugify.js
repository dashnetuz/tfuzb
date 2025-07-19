function slugify(text) {
    const cyr = ['а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
        'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
        'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
        'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'];
    const lat = ['a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p',
        'r','s','t','u','f','x','ts','ch','sh','sch','','y','','e','yu','ya',
        'a','b','v','g','d','e','yo','zh','z','i','y','k','l','m','n','o','p',
        'r','s','t','u','f','x','ts','ch','sh','sch','','y','','e','yu','ya'];

    for (let i = 0; i < cyr.length; i++) {
        text = text.replace(new RegExp(cyr[i], 'g'), lat[i]);
    }

    return text.toLowerCase()
        .replace(/\s+/g, '-')       // bo'sh joylar → defis
        .replace(/[^\w\-]+/g, '')   // harf, raqam, defis
        .replace(/\-\-+/g, '-')     // ketma-ket defis
        .replace(/^-+/, '')         // boshlang'ich defis
        .replace(/-+$/, '');        // oxirgi defis
}

// 🔁 Binding funksiyasi
function bindSlugify(sourceSelector, targetSelector) {
    const source = document.querySelector(sourceSelector);
    const target = document.querySelector(targetSelector);

    if (source && target) {
        source.addEventListener('input', function () {
            target.value = slugify(source.value);
        });
    }
}
