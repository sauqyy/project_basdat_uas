// Smooth scroll for internal links
(function(){
  function smoothScrollTo(targetY){
    try{window.scrollTo({top:targetY,behavior:'smooth'})}catch(_){window.scrollTo(0,targetY)}
  }

  document.addEventListener('click',function(e){
    const a=e.target.closest('a[href^="#"]');
    if(!a) return;
    const href=a.getAttribute('href');
    if(href==='#' || href==='#0') return;
    const id=href.slice(1);
    const el=document.getElementById(id);
    if(!el) return;
    e.preventDefault();
    const rect=el.getBoundingClientRect();
    const y=rect.top+window.pageYOffset-64; // account header
    smoothScrollTo(y);
  });

  // Scroll-to-top button behavior
  document.querySelectorAll('[data-scroll-top]').forEach(function(btn){
    btn.addEventListener('click',function(e){
      // if it is an anchor we already handle, skip here
      const isAnchor=btn.tagName.toLowerCase()==='a' && btn.getAttribute('href')?.startsWith('#');
      if(isAnchor) return;
      e.preventDefault();
      smoothScrollTo(0);
    });
  });
  
  // Toggle header style when sticky header overlaps light sections
  const header=document.querySelector('.site-header');
  const lightSections=[...document.querySelectorAll('.section-white, .section-soft')];
  function updateHeaderTone(){
    if(!header||!lightSections.length) return;
    const headerHeight=header.getBoundingClientRect().height||64;
    let onLight=false;
    for(const sec of lightSections){
      const r=sec.getBoundingClientRect();
      // If the top of the section is at or above the header and section is still visible
      if(r.top<=headerHeight && r.bottom>0){
        onLight=true;break;
      }
    }
    header.classList.toggle('on-light',onLight);
  }
  updateHeaderTone();
  window.addEventListener('scroll',updateHeaderTone,{passive:true});
  window.addEventListener('resize',updateHeaderTone);
  
  // Emoji rating click feedback
  document.querySelectorAll('.emoji-rating .emoji').forEach(function(btn){
    btn.addEventListener('click',function(){
      const all=[...document.querySelectorAll('.emoji-rating .emoji')];
      all.forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
    });
  });
})();

