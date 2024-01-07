$(document).ready(function() {
    new ClipboardJS('#copy_link_replica');
    new ClipboardJS('#copy_link_lcp');
});

function twittershare(url) {
    // Opens a pop-up with twitter sharing dialog
    window.open('http://twitter.com/share?url=' + encodeURIComponent(url), '', 'left=0,top=0,width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');
}

function googlePlusShare(url) {
    var url1 = encodeURIComponent(url);
    window.open('https://plus.google.com/share?url=' + url1, '', 'left=0,top=0,width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');
}
    
function facebookShare(url) {
    window.open(url, '', 'left=0,top=0,width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');
}

function linkedInShare(url) {
    window.open('http://www.linkedin.com/shareArticle?url=' + encodeURIComponent(url), '', 'left=0,top=0,width=550,height=450,personalbar=0,toolbar=0,scrollbars=0,resizable=0');
}