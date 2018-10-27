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
   <h2>Ajanvaraus</h2>
   <div class="main-content">
   *Ajanvaraus päivälle <span class="date"></span>*
   <p/>
   <div class="contact-info">Ajankohta: </div>
   <div>
   <select class="start"></select>
   </div>
   <div class="contact-info">Nimi: </div><input type="text" name="name" class="name" />
   <div class="contact-info">Sähköposti: </div><input type="text" name="email" class="email" />
   <div class="contact-info">Puhelin: </div><input type="text" name="phone" class="phone" />
   <div class="contact-info">Lisätietoja: </div><textarea rows="6" cols="40" class="comment"></textarea>
   <div class="general-error error">Varaus epäonnistui. Yritä hetken kuluttua uudestaan.</div>
   <input class="add-appointment-button" type="button" value="Varaa aika" />
  </div>
  <div class="duplicate-booking error">
   <div>Valitettavasti varaus epäonnistui. Joku ehti juuri varata kyseisen ajan. Ole hyvä ja valitse uusi aika.</div>
  </div>
  <div class="success">
   <div>Kiitos varauksesta! Saat pian vielä sähköpostiisi vahvistuksen.</div>
  </div>
  </div>
</div>

Ajatus & Asana tilaustuntien ja yksityisvalmennusten ajanvaraus
===============================================================

<div class="info">
Napsauta sinistä laatikkoa halutun päivän kohdalta jolloin saat vapaat ajat näkyviin. Kaikki valikossa tarjolla olevat ajat ovat 75 min pituisia.
</div>

<div id='calendar'></div>

<div class="logout">
  <input type="button" class="logout-button" value="Kirjaudu ulos"></input>
</div>

