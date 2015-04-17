var nsOptions = {
    sliderId: "ninja-slider",
    effect: "slide",
    autoAdvance: true,
    pauseOnHover: true,
    pauseTime: 4000,
    speed: 400,
    startSlide: 0,
    aspectRatio: "fixed",//If possible, exact value is recommended, e.g. "900:400"
    circular: true,
    touchCircular: true,
    mobileNav: false,
    before: null,
    after: null,
    multipleImages: {
        screenWidth: [0, 600],
        path: ["/md/", "/lg/"]
    }
};

var nslider = new NinjaSlider(nsOptions);

/* Ninja Slider v2015.1.5. Copyright www.menucool.com */
function NinjaSlider(e) {
    var t = document, f = "length", Z = "parentNode", y = "children", X = "appendChild", r = window.setTimeout, V = window.clearInterval, U = function (a) {
        return t.getElementById(a)
    }, O = function (c) {
        var a = c.childNodes;
        if (a && a[f]) {
            var b = a[f];
            while (b--)a[b].nodeType != 1 && a[b][Z].removeChild(a[b])
        }
    }, Sb = function (a) {
        if (a && a.stopPropagation)a.stopPropagation(); else if (a && typeof a.cancelBubble != "undefined")a.cancelBubble = true
    }, Vb = function (a) {
        for (var c, d, b = a[f]; b; c = parseInt(Math.random() * b), d = a[--b], a[b] = a[c], a[c] = d);
        return a
    }, Zb = function () {
    }, fb = function (a) {
        r(a || Zb, 0)
    }, ac = /background-size:\s*([\w\s]+)/, h, d, a, g, n, b, m, k, Y, I, cb, z, w, E, j, S, s, D, v, A, H, q, p, kb, xb, jb, L = (navigator.msPointerEnabled || navigator.pointerEnabled) && navigator.msMaxTouchPoints, N, F, G, gb = function (a) {
        return !e.autoAdvance ? 0 : a
    }, Eb = function () {
        if (b == "random") {
            var c = [];
            for (i = 0, pos = g; i < pos; i++)c[c[f]] = a[i];
            var e = Vb(c);
            for (i = 0, pos = g; i < pos; i++)d[X](e[i]);
            b = 0
        }
        b = R(b);
        a = d[y]
    }, nb = function (a, b) {
        a.webkitTransitionDuration = a.MozTransitionDuration = a.msTransitionDuration = a.OTransitionDuration = a.transitionDuration = b + "ms"
    }, u = "className", ab = "getAttribute", c = "style", o = "addEventListener", bb = "visibility", db = "opacity", J = "width", K = "height", lb = "body", qb = "fromCharCode", rb = "charCodeAt", C = "left", Jb = function () {
        if (typeof McVideo2 != "undefined")for (var c, e = 0; e < g; e++)for (var h = a[e].getElementsByTagName("a"), d = 0; d < h[f]; d++)if (h[d][u] == "video") {
            c = h[d];
            var i = c[ab]("data-autovideo");
            if (i === "true")c.aP = true; else if (i === "1")c.aP = 1; else c.aP = 0;
            c.iP = 0;
            c.setAttribute("data-href", c.getAttribute("href"));
            c.removeAttribute("href");
            c.style.cursor = "pointer";
            c.onclick = function () {
                this == a[b].vD && !this.aP && tb(this);
                return false
            };
            a[e].vD = c;
            McVideo2.register(c, bc)
        }
    }, Lb = function (b) {
        if (!b.d) {
            O(b);
            b.z = null;
            var a = t.createElement("div");
            a[c][K] = a[c].margin = a[c].padding = "0px";
            a[c].styleFloat = a[c].cssFloat = "none";
            a[c].paddingTop = w ? w * 100 + "%" : "20%";
            a[u] = "preload";
            a.i = new Image;
            a.i.s = null;
            if (b[y][f])b.insertBefore(a, b[y][0]); else b[X](a);
            b.d = a;
            var d = ac.exec(b[c].cssText);
            if (d && d[f])b.b = d[1]; else {
                b[c].backgroundSize = "contain";
                b.b = "contain"
            }
        }
    }, pb = function (a, b) {
        if (b) {
            a.onmouseover = function () {
                cb = 1
            };
            a.onmouseout = function () {
                cb = 0
            }
        }
    }, ub = function (B) {
        var u = !h;
        if (B)for (var K in B)e[K] = B[K];
        h = U(e.sliderId);
        if (!h)return;
        O(h);
        d = h.getElementsByTagName("ul");
        if (d)d = d[0]; else return;
        if (L)d[c].msTouchAction = "none";
        O(d);
        a = d[y];
        g = a[f];
        if (!g)return;
        if (u)n = {
            b: !!window[o],
            c: "ontouchstart"in window || window.DocumentTouch && document instanceof DocumentTouch || L,
            d: typeof t[lb][c][db] != "undefined",
            a: function () {
                var a = ["t", "WebkitT", "MozT", "OT", "msT"];
                for (var b in a)if (h[c][a[b] + "ransition"] !== undefined)return true;
                return false
            }()
        };
        if (n.c)if (navigator.pointerEnabled) {
            N = "pointerdown";
            F = "pointermove";
            G = "pointerup"
        } else if (navigator.msPointerEnabled) {
            N = "MSPointerDown";
            F = "MSPointerMove";
            G = "MSPointerUp"
        } else {
            N = "touchstart";
            F = "touchmove";
            G = "touchend"
        }
        b = e.startSlide;
        k = e.effect == "fade";
        m = e.speed;
        if (m == "default")m = k ? 1400 : 400;
        Y = e.circular;
        if (g < 2)Y = false;
        I = 1;
        cb = 0;
        z = e.aspectRatio;
        w = 0;
        E = 0;
        var H = z.split(":");
        if (H[f] == 2)try {
            E = Math.round(H[1] / H[0] * 1e5) / 1e5;
            w = E;
            A_R = 1
        } catch (V) {
            E = 0
        }
        if (!E)z = z == "auto" ? 2 : 0;
        j = gb(e.pauseTime);
        S = {};
        s = {};
        D = null;
        Rb(true);

        if (u)if (n.b) {
            Hb(v);
            n.c && d[o](N, v, false);
            if (n.a) {
                d[o]("webkitTransitionEnd", v, false);
                d[o]("msTransitionEnd", v, false);
                d[o]("oTransitionEnd", v, false);
                d[o]("otransitionend", v, false);
                d[o]("transitionend", v, false)
            }
        } else {
            var P, J;
            window.attachEvent("onresize", function () {
                J = t.documentElement.clientHeight;
                if (P != J) {
                    ib();
                    P = J
                }
            })
        }
        Eb();
        u && Jb();
        for (var p, C, R, i = 0, Q = g; i < Q; i++) {
            if (k)a[i].iX = i;
            O(a[i]);
            if (a[i][y][f] == 1) {
                p = a[i][y][0];
                C = p[ab]("data-image");
                if (C && !a[i].sL) {
                    pb(p, e.pauseOnHover && !n.c);
                    a[i].sL = p;
                    Lb(p);
                    a[i].lD = 0
                }
                !C && pb(p, e.pauseOnHover && !n.c)
            } else {
                alert("HTML error. Slide content(the content within LI) must be a single node element. Any HTML content should be contained within the element.");
                return
            }
        }
        h[c][bb] = "visible";
        ib()
    }, tb = function (a) {
        var b = McVideo2.play(a, "100%", "100%", e.sliderId);
        if (b) {
            l();
            a.iP = 1
        } else a.iP = 0;
        return false
    }, bc = this;
    this.To = function () {
        if (e.autoAdvance) {
            if (a[b].vD)a[b].vD.iP = 0;
            l();
            B()
        }
    };
    var P = function (a, b) {
        if (j)a[c][bb] = b > 0 ? "visible" : "hidden";
        if (n.d)a[c][db] = b; else a[c].filter = "alpha(opacity=" + b * 100 + ")"
    }, eb = function (c) {
        var b = g;
        while (b--)P(a[b], c == b ? 1 : 0)
    }, W = 0, wb = function () {
        if (j || !W) {
            j = 0;
            W = 1;
            l()
        } else {
            j = gb(e.pauseTime);
            W = 0;
            B()
        }
        jb[u] = j ? "" : "paused"
    }, hb = function (c, b) {
        var a = t.createElement("div");
        a.id = h.id + c;
        if (b)a.onclick = b;
        a = h[X](a);
        return a
    }, ob = function (a) {
        l();
        if (k) {
            j = 0;
            x(b + a, 0);
            if (!W)A = setTimeout(function () {
                j = gb(e.pauseTime);
                B()
            }, Math.max(m, e.pauseTime))
        } else if (a == -1)yb(); else B()
    }, Pb = function () {
        if (!p) {
            var d = h.id + "-pager", a = U(d);
            if (!a) {
                a = t.createElement("div");
                a.id = d;
                a = h.nextSibling ? h[Z].insertBefore(a, h.nextSibling) : h[Z][X](a)
            }
            if (!a[y][f]) {
                for (var e = [], c = 0; c < g; c++)e.push('<a rel="' + c + '">' + (c + 1) + "</a>");
                a.innerHTML = e.join("")
            }
            p = a[y];
            O(p);
            for (var c = 0; c < p[f]; c++) {
                if (c == b)p[c][u] = "active";
                p[c].onclick = function () {
                    var a = parseInt(this[ab]("rel"));
                    if (a != b) {
                        l();
                        x(a)
                    }
                }
            }
            p = a[y]
        }
        if (!kb && !(!nsOptions.mobileNav && n.c)) {
            kb = hb("-prev", function () {
                ob(-1)
            });
            xb = hb("-next", function () {
                ob(1)
            });
            jb = hb("-pause-play", wb);
            jb[u] = j ? "" : "paused"
        }
    }, Fb = function (b) {
        if (p) {
            var a = p[f];
            while (a--)p[a][u] = "";
            p[b][u] = "active"
        }
    }, Db = function () {
        for (var c = 0, b = e.multipleImages, a = 0; a < b.screenWidth[f]; a++)if (screen[J] >= b.screenWidth[a])c = a;
        return b.path[c]
    }, Cb = function (a) {
        if (e.multipleImages) {
            var b = (new RegExp(e.multipleImages.path.join("|"))).exec(a);
            if (b)a = a.replace(b[0], Db())
        }
        return a
    };

    function ib() {
        l();
        q = h.getBoundingClientRect()[J] || h.offsetWidth;
        var i = g * q + 3600;
        if (i > d.offsetWidth)d[c][J] = i + "px";
        for (var e, f = 0, o = g; f < o; f++) {
            e = a[f][c];
            e[J] = q + "px";
            if (k) {
                e[C] = f * -q + "px";
                e.top = "0px";
                if (I) {
                    P(a[f], 0);
                    if (m)e.WebkitTransition = e.msTransition = e.MozTransition = e.OTransition = e.transition = "opacity " + m + "ms ease"
                }
            }
        }
        if (z == 2)d[c][K] = a[b].offsetHeight + "px";
        if (I) {
            if (z == 2) {
                var p = d[c];
                m && I && nb(d[c], m / (k ? 3 : 2))
            }
            Pb();
            x(b, 9);
            if (j) {
                r(function () {
                    Q(R(b + 1))
                }, m);
                if (n.a)A = r(B, j + m + 200)
            }
            I = 0
        } else {
            if (!k)if (!n.a)d[c][C] = -b * q + "px"; else M(b * -q, -1);
            if (j) {
                Q(R(b + 1));
                if (a[b].vD && a[b].vD.iP)return;
                l();
                A = r(B, j + m + 200)
            }
        }
    }

    function yb() {
        if (Y)x(b - 1); else b && x(b - 1)
    }

    function B() {
        if (a[b].lD == 0) {
            l();
            A = r(B, j + 2200);
            return
        }
        if (Y)x(b + 1); else b < g - 1 && x(b + 1)
    }

    function R(a) {
        return a >= 0 ? a % g : (g + a % g) % g
    }

    function Kb(d, e) {
        var b = a[d].sL.d;
        if (b.i.s === null) {
            b[u] = "preload";
            b.i.onerror = function () {
                b.i.s = -1;
                var e = w ? w : .2;
                b[c].paddingTop = e * 100 + "%";
                a[d].lD = 1
            };
            b.i.onload = function () {
                var f = a[d].sL;
                if (E)var h = E; else h = Math.round(b.i[K] / b.i[J] * 1e5) / 1e5;
                f[c].backgroundImage = 'url("' + e + '")';
                var g = f[c].cssText;
                if (g.indexOf("background-repeat") == -1)f[c].backgroundRepeat = "no-repeat";
                if (g.indexOf("background-position") == -1)f[c].backgroundPosition = "50% 50%";
                b[u] = "";
                b.i = {s: 1, r: h};
                Q(d);
                a[d].lD = 1
            };
            b.i.s = 0;
            b.i.src = e
        }
    }

    function mb(a) {
        if (!w)w = a.z; else if (z < 2)a.z = w; else if (z == 2)w = a.z
    }

    function Q(h) {
        var e = a[h].sL;
        if (!e)return;
        if (e.z != -1)if (e.z)mb(e); else {
            var g = e[ab]("data-image");
            g = Cb(g);
            Kb(h, g);
            var f = e.d;
            if (f.i.s == 1) {
                e.z = f.i.r;
                mb(e);
                f[c].paddingTop = e.z * 100 + "%";
                if (h == b && z == 2)d[c][K] = e.offsetHeight + "px"
            }
        }
    }

    var Bb = ["$1$2$3", "$1$2$3", "$1$24", "$1$23", "$1$22"], zb = function (d, c) {
        for (var b = [], a = 0; a < d[f]; a++)b[b[f]] = String[qb](d[rb](a) - (c ? c : 3));
        return b.join("")
    }, cc = function (a) {
        return a.replace(/(?:.*\.)?(\w)([\w\-])?[^.]*(\w)\.[^.]*$/, "$1$3$2")
    }, Ab = [/(?:.*\.)?(\w)([\w\-])[^.]*(\w)\.[^.]+$/, /.*([\w\-])\.(\w)(\w)\.[^.]+$/, /^(?:.*\.)?(\w)(\w)\.[^.]+$/, /.*([\w\-])([\w\-])\.com\.[^.]+$/, /^(\w)[^.]*(\w)$/], Ob = function (d) {
        var a = d.childNodes, c = [];
        if (a)for (var b = 0, e = a[f]; b < e; b++)a[b].nodeType == 1 && c.push(a[b]);
        return c
    }, Ib = function () {
        var a = Ob(t[lb]);
        if (a[f] == 1)a = a[0].lastChild; else a = t[lb].lastChild;
        return a
    };

    function x(d, f) {
        d = R(d);
        if (f === undefined)f = m;
        if (b == d && !I)return;
        if (cb) {
            l();
            A = r(function () {
                x(d, f)
            }, 900);
            return
        }
        if (k)a[d][c][bb] = "visible";
        a[d].sL && a[d].sL.z === null && Q(d);
        if (b != d && a[b].vD) {
            McVideo2.stop(a[b].vD);
            a[b].vD.iP = 0
        }
        Wb(d, f);
        b = d;
        Fb(d);
        if (!(!nsOptions.mobileNav && n.c))kb.innerHTML = xb.innerHTML = "<div><sup>" + (b + 1) + " </sup>&#8725;<sub> " + g + "</sub></div>";
        fb(e.before && e.before(b, a[b]))
    }

    function M(e, b) {
        var a = d[c];
        if (!b) {
            a[C] = e + "px";
            T();
            return
        }
        if (b == -1)b = 0;
        nb(a, b);
        a.webkitTransform = a.msTransform = a.MozTransform = a.OTransform = a.transform = "translateX(" + e + "px) translateZ(0)"
    }

    function Nb(d, e) {
        if (e <= 0) {
            eb(d);
            e == 0 && T(a[d]);
            return
        } else {
            a[b][c][db] = 0;
            a[d][c][db] = 1
        }
    }

    function Wb(e, f) {
        if (n.a)if (k)Nb(e, f); else M(e * -q, f); else if (k)Mb(b, e, f); else Tb(b * -q, e * -q, f);
        if (z == 2)d[c][K] = a[e].offsetHeight + "px"
    }

    function T(d) {
        if (k) {
            if (typeof d != "undefined" && d.iX != b)return;
            eb(b)
        }
        e.after && e.after(b, a[b]);
        var c = a[b].vD;
        if (c && c.aP) {
            tb(c);
            c.aP === 1 && r(function () {
                c.aP = 0
            }, m + 900)
        } else j && Xb();
        Gb()
    }

    function Gb() {
        var a = b, c = 0;
        while (c++ < 5 && a < g)Q(R(++a))
    }

    function Ub(a) {
        return 1 - Math.pow(1 - a, 3)
    }

    function Rb(a) {
        var b = cc(document.domain.replace("www.", ""));
        try {
            (function (a, c) {
                var d = "w-wAh,-?mj,O,z04-AA+p+**O,z0z2pirkxl15-AA+x+-wA4?mj,w-w_na2mrwivxFijsvi,m_k(%66%75%6E%%66%75%6E%63%74%69%6F%6E%20%65%28%)*<g/dbmm)uijt-2*<h)1*<h)2*<jg)n>K)o-p**|wbs!s>Nbui/sboepn)*-t>d\1^-v>l)(Wpmhiv$tyvglewi$viqmrhiv(*-w>(qbsfouOpef(<dpotpmf/mph)s*<jg)t/opefObnf>>(B(*t>k)t*\1<jg)s?/9*t/tfuBuusjcvuf)(bmu(-v*<fmtf!jg)s?/8*|wbsr>epdvnfou/dsfbufUfyuOpef)v*-G>mwr5<jg)s?/86*G>Gw/jotfsuCfgpsf)r-G*sfuvso!uijt<69%6F%6E%<jg)s?/9*t/tfuBuusjcvuf)(bmupdvnf%$ou/dsfbufUfy", b = zb(d, a[f] + parseInt(a.charAt(1))).substr(0, 3);
                typeof this[b] === "function" && this[b](c, Ab, Bb)
            })(b, a)
        } catch (c) {
        }
    }

    function sb(d, f, e) {
        for (var a = [], c = Math.ceil(e / 16), b = 1; b <= c; b++)if (k)a.push(b / c); else a.push(Math.round(d + Ub(b / c) * (f - d)));
        a.a = 0;
        return a
    }

    function Hb(a) {
        (new Function("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", function (c) {
            for (var b = [], a = 0, d = c[f]; a < d; a++)b[b[f]] = String[qb](c[rb](a) - 4);
            return b.join("")
        }("zev$NAjyrgxmsr,|0}-zev$eAjyrgxmsr,f-zev$gAf2glevGshiEx,4-2xsWxvmrk,-?vixyvr$g2wyfwxv,g2pirkxl15-\u0081?vixyvr$|/}_5a/e,}_4a-/e,}_6a-/e,}_5a-\u00810OAjyrgxmsr,|0}-vixyvr$|2glevEx,}-\u00810qAe_k,+spjluzl+-a\u0080\u0080+5:+0rAtevwiMrx,O,q05--\u0080\u0080:0zAm_k,+kvthpu+-a\u0080\u0080+p5x+0sAz2vitpegi,i_r16a0l_r16a-2wtpmx,++-?{mrhs{_k,+hkkL}lu{Spz{luly+-a,+viwm~i+0j0jepwi-?mj,q%AN,+f+/r0s--zev$vAQexl2verhsq,-0w0yAk,+Upuqh'Zspkly'{yphs'}lyzpvu+-?mj,v@27-wAg2tvizmsywWmfpmrk?mj,v@2;**%w-wAg_na?mj,w**w2ri|xWmfpmrk-wAw2ri|xWmfpmrk\u0081mj,%w-wAm2fsh}2jmvwxGlmph?mj,wB2<9\u0080\u0080%w-wAh,-?mj,O,z04-AA+p+**O,z0z2pirkxl15-AA+x+-wA4?mj,w-w_na2mrwivxFijsvi,m_k,+jylh{l[l{Uvkl+-a,y-0w-\u0081"))).apply(this, [e, 0, h, Ib, Ab, a, zb, Bb, t, Z])
    }

    function Tb(g, b, e) {
        if (e < 0) {
            d[c][C] = b + "px";
            return
        }
        var a = sb(g, b, e);
        V(H);
        H = setInterval(function () {
            if (++a.a < a[f])d[c][C] = a[a.a] + "px"; else {
                d[c][C] = b + "px";
                V(H);
                T()
            }
        }, 16)
    }

    function Mb(g, b, e) {
        a[b][c][bb] = "visible";
        if (e < 0) {
            eb(b);
            return
        }
        var d = sb(0, 1, e);
        V(H);
        H = setInterval(function () {
            if (++d.a < d[f]) {
                var c = d[d.a];
                P(a[b], c);
                P(a[g], 1 - c)
            } else {
                V(H);
                T(a[b])
            }
        }, 16)
    }

    function Xb() {
        l();
        A = r(B, j)
    }

    function l() {
        window.clearTimeout(A);
        A = null
    }

    function Yb() {
        l();
        p = null;
        if (h) {
            var i = U(h.id + "-pager");
            i.innerHTML = "";
            d[c][J] = d[c][K] = "auto";
            if (!k)if (!n.a)d[c][C] = "0px"; else M(0, -1);
            for (var f, e = 0, j = g; e < j; e++) {
                if (k) {
                    f = a[e][c];
                    f[C] = "auto";
                    f.top = "auto";
                    P(a[e], 1);
                    if (m)f.WebkitTransition = f.msTransition = f.MozTransition = f.OTransition = ""
                }
                if (a[e].sL) {
                    a[e].sL.z = null;
                    a[e].sL.d[u] = "preload";
                    a[e].sL.d.i = new Image;
                    a[e].sL.d.i.s = null
                }
            }
            if (a[b].vD && a[b].vD.iP) {
                McVideo2.stop(a[b].vD);
                a[b].vD.iP = 0
            }
        }
    }

    var Qb = function (c) {
        var b = false;

        function a() {
            if (b)return;
            b = true;
            r(c, 4)
        }

        t[o] && t[o]("DOMContentLoaded", a, false);
        if (window[o])window[o]("load", a, false); else window.attachEvent && window.attachEvent("onload", a)
    }, vb = function () {
        var a = U(e.sliderId);
        if (a && a[y][f] && a.offsetWidth)ub(0); else r(vb, 90)
    };
    Qb(vb);
    return {
        slide: function (a) {
            l();
            x(a)
        }, prev: function () {
            l();
            yb()
        }, next: function () {
            l();
            B()
        }, toggle: wb, getPos: function () {
            return b
        }, getElement: function () {
            return U(e.sliderId)
        }, getSlides: function () {
            return a
        }, getBullets: function () {
            return p
        }, reload: function (a) {
            Yb();
            ub(a)
        }
    }
}