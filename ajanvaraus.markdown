---
title: Ajanvaraus
---

<div class="popup add-time-slot-popup">
  <div class="popup-content">
   <img class="close" src="/img/popup_close.png" />
   <h2>Uusi vapaa aika</h2>
   <div class="contact-info">Alku (esim. 12:00): </div><input type="text" class="start" />
   <div class="contact-info">Loppu (esim. 16:30): </div><input type="text" class="end" />
   <div class="error">Ajan muokkaus epäonnistui. Yritä hetken kuluttua uudestaan.</div>
   <input class="add-time-slot-button" type="button" value="Päivitä" />
  </div>
</div>

<div class="popup add-appointment-popup">
  <div class="popup-content">
   <img class="close" src="/img/popup_close.png" />
   <h2>Varaa aika</h2>
   <div>
   <select class="start"></select>
   </div>
   <div class="contact-info">Nimi: </div><input type="text" class="name" />
   <div class="contact-info">Sähköposti: </div><input type="text" class="email" />
   <div class="contact-info">Puhelin: </div><input type="text" class="phone" />
   <div class="contact-info">Lisätietoja: </div><input type="textarea" class="comment" />
   <div class="error">Varaus epäonnistui. Yritä hetken kuluttua uudestaan.</div>
   <input class="add-appointment-button" type="button" value="Varaa aika" />
  </div>
</div>

<div class="popup appointment-failed-popup">
  <div class="popup-content">
   <img class="close" src="/img/popup_close.png" />
   <h2>Varaus epäonnistui</h2>
   <div>Valitettavasti varaus epäonnistui. Joku ehti juuri varata kyseisen ajan. Ole hyvä ja valitse uusi aika.</div>
  </div>
</div>

Ajatus & Asana ajanvaraus
=========================

<div class="info">
Napsauta sinistä laatikkoa halutun päivän kohdalta jolloin saat vapaat ajat näkyviin.
</div>

<div id='calendar'></div>

<div class="logout">
  <input type="button" class="logout-button" value="Kirjaudu ulos"></input>
</div>

