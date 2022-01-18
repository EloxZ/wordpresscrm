

function wplc_convertDateToTicks(dt) {
    const epochTicks = 621355968000000000;// ticks between 0 and 01/01/1970 which is starting date of javascript
    const seconds = dt.getTime();
    const dateInSeconds = seconds;
    // multiply by 10000 to reconcile to c#
    return (dateInSeconds * 10000) + epochTicks;
}

function wplc_convertTicksToDate(ticks) {
    var epochTicks = 621355968000000000,
        ticksPerMillisecond = 10000,
        jsTicks = 0,
        jsDate,

        jsTicks = (ticks - epochTicks) / ticksPerMillisecond;

    jsDate = new Date(jsTicks);
    return jsDate.getTime()+(jsDate.getTimezoneOffset()*60*1000);
}


function wplc_stringToColor(str) {
    if(typeof str =='undefined')
    {
        str='Guest';
    }
    var hash = 0;
    for (var i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }

    var h = hash % 360;
    return wplc_hslToHex(h,30,40);
}

function wplc_hslToHex(h, s, l) {
    h /= 360;
    s /= 100;
    l /= 100;
    let r, g, b;
    if (s === 0) {
        r = g = b = l; // achromatic
    } else {
        const hue2rgb = (p, q, t) => {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1 / 6) return p + (q - p) * 6 * t;
            if (t < 1 / 2) return q;
            if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
            return p;
        };
        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        r = hue2rgb(p, q, h + 1 / 3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1 / 3);
    }
    const toHex = x => {
        const hex = Math.round(x * 255).toString(16);
        return hex.length === 1 ? '0' + hex : hex;
    };
    return `${toHex(Math.abs(r.toFixed(2)))}${toHex(Math.abs(g.toFixed(2)))}${toHex(Math.abs(b.toFixed(2)))}`;
}

function wplc_isDoubleByte(str) {
    for (var i = 0, n = str.length; i < n; i++) {
        if (str.charCodeAt( i ) > 255) { return true; }
    }
    return false;
}

function wplc_decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}

function wplc_lightenDarkenColor(color, percent) {
        var num = parseInt(color.replace("#",""),16),
            amt = Math.round(2.55 * percent),
            R = (num >> 16) + amt,
            B = (num >> 8 & 0x00FF) + amt,
            G = (num & 0x0000FF) + amt;
        var result = "#" + (0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (B<255?B<1?0:B:255)*0x100 + (G<255?G<1?0:G:255)).toString(16).slice(1);
        return result;
}