<!doctype html>
<html>

<head>
  <meta name="viewport" content="width=device-width" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>{{ config('app.name') }}</title>
</head>
<!--explodehere-->
<body class="">
  <p>
    <small class="text-muted">
      {{ __('Mail/Intro.note', ['name' => $notifiable->name]) }}
    </small>
  </p>
  <div class="row text-right">
    <div class="col-md-12">
      <h2>{!! __('Mail/Contract/ContractNotification.title') !!}</h2>
      <p><strong>{!! __('Mail/Contract/ContractNotification.number') !!}<em>{!! $contract->number !!}</em><br />
      {!! __('Mail/Contract/ContractNotification.client') !!}<em>{!! $contract->client_id !!}</em></strong></p>
    </div>
  </div>
  <div class="row text-left">
    <div class="col-md-12">
      <p>
        @if ($notifiable->metadata)
          @if ($notifiable->metadata->gender == 'male')
            {{ __('Mail/Intro.male', ['name' => $notifiable->name]) }}
          @elseif($notifiable->metadata->gender == 'female')
            {{ __('Mail/Intro.female', ['name' => $notifiable->female]) }}
          @else
            {{ __('Mail/Intro.unknown', ['name' => $notifiable->name]) }}
          @endif
        @else
          {{ __('Mail/Intro.unknown', ['name' => $notifiable->name]) }}
        @endif
      </p>
      <p>
        {!! __('Mail/Contract/ContractNotification.date', ['date' => $contract->created_at]) !!}
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div id="printMe" style="font-size: 3em;">
          <table width="100%">
                <tr>
                      <td width="50%" align="left" class="text-left">
                            <p class="small-text"><small><strong>TecSee UG (haftungsbeschränkt), Biberacherstr. 53, 74078 Heilbronn</strong></small></p>
                      </td>
                </tr>
                <tr>
                      <td width="50%" align="left" class="text-left align-top">
                            <p class="text-left">
                                  {!!$contract->client->name!!} <br>
                                  @if($contract->client->metadata)
                                    @if($contract->client->metadata->first_name && $contract->client->metadata->last_name)
                                      @if($contract->client->metadata->gender == 'male')
                                        Herr {!!$contract->client->metadata->first_name!!} {!!$contract->client->metadata->last_name!!} <br>
                                      @elseif($contract->client->metadata->gender == 'female')
                                        Frau {!!$contract->client->metadata->first_name!!} {!!$contract->client->metadata->last_name!!} <br>
                                      @else
                                        Herr/Frau {!!$contract->client->metadata->first_name!!} {!!$contract->client->metadata->last_name!!} <br>
                                      @endif
                                    @elseif($contract->client->metadata->first_name)
                                      @if($contract->client->metadata->gender == 'male')
                                        Herr {!!$contract->client->metadata->first_name!!} <br>
                                      @elseif($contract->client->metadata->gender == 'female')
                                        Frau {!!$contract->client->metadata->first_name!!} <br>
                                      @else
                                        Herr/Frau {!!$contract->client->metadata->first_name!!} <br>
                                      @endif
                                    @elseif($contract->client->metadata->last_name)
                                      @if($contract->client->metadata->gender == 'male')
                                      <template v-if="contract.client.metadata.gender == 'male'">
                                        Herr {!!$contract->client->metadata->last_name!!} <br>
                                      </template>
                                    @elseif($contract->client->metadata->gender == 'female')
                                        Frau {!!$contract->client->metadata->last_name!!} <br>
                                      @else
                                        Herr/Frau {!!$contract->client->metadata->last_name!!} <br>
                                      @endif
                                    @endif
                                    @if($contract->client->metadata->street_number)
                                      {!!$contract->client->metadata->street_number!!} <br>
                                    @endif
                                    @if($contract->client->metadata->address)
                                      {!!$contract->client->metadata->address!!} <br>
                                    @endif
                                  @endif
                            </p>
                      </td>
                      <td width="50%" align="left" class="text-left">
                            <p class="text-left pl-5">
                                  TecSee UG (haftungsbeschränkt) <br>
                                  @if($contract->creator->metadata)
                                    {!!$contract->creator->metadata->first_name!!} {!!$contract->creator->metadata->last_name!!} <br>
                                  @endif
                                  Biberacherstr. 53 <br>
                                  74078 Heilbronn <br>
                                  Tel: 07131 390 2637 <br>
                                  Web: www.tecsee.de <br>
                                  E-Mail: {!!$contract->creator->email!!} <br>
                                  @if($contract->creator->metadata)
                                    Bearbeiter: {!!$contract->creator->metadata->first_name!!} {!!$contract->creator->metadata->last_name!!}
                                  @endif
                            </p>
                      </td>
                </tr>
          </table>
          <table width="100%">
                <tr>
                      <td width="100%" class="text-center"><h3><strong>Vertrag</strong></h3></td>
                      <td></td>
                </tr>

          </table>
          <table width="100%">
                <tr>
                      <td width="50%" class="text-left" align="left">
                            <p>
                                  <strong>
                                        Auftragsnr.{!!$contract->number!!} <br>
                                        Kundennummer: {!!$contract->client->id!!}
                                  </strong>
                            </p>
                      </td>
                       <td width="50%" class="text-right align-middle" align="right">
                            {!!$contract->created_at!!}
                      </td>
                </tr>
          </table>
          <table width="100%">
                 <thead class="text-center">
                       <tr class="pb-2 mb-4" style="border-bottom: 1px solid #000;">
                             <th>Menge</th>
                             <th>Bezeichnung</th>
                             <th></th>
                             <th>Einzelpreis</th>
                             <th>Gesamtpreis</th>
                       </tr>
                 </thead>
                 <tbody class="text-center">
                    @foreach($contract->contract_items as $item)
                       <tr class="pt-2">
                         <td>{!!$item->qty!!}</td>
                         <td class="text-left" style="word-wrap: break-word;min-width: 300px;max-width: 300px;">{!!$item->name!!}</td>
                         <td></td>
                         <td class="text-right">{!!$item->price!!}</td>
                         <td class="text-right">{!!$item->total_price!!}</td>
                       </tr>
                       <tr>
                         <td></td>
                         <td colspan="2" style="word-wrap: break-word;min-width: 300px;max-width: 300px;" class="text-left pl-5">** {!!$item->description!!}</td>
                         <td></td>
                         <td></td>
                       </tr>
                    @endforeach
                 </tbody>
                 <tfoot class="text-right">
                       <tr>
                             <td></td>
                             <td></td>
                             <td>zzgl. 19,00% MwSt. in EUR</td>
                             <td></td>
                             <td>{!!$contract->total_tax!!}</td>
                       </tr>
                       <tr style="border-bottom: 1px solid #000;">
                            <td></td>
                            <td></td>
                            <td><strong>Gesamt Bruttobetrag in EUR</strong></td>
                            <td></td>
                            <td><strong>{!!$contract->total!!}</strong></td>
                       </tr>
                 </tfoot>
          </table>
          <table width="100%">
            <tr>
              <td style="border-bottom: 1px solid #000; padding-top: 8px;">
                <p>Die Rahmenbedingungen werden dem Auftrag hinzugefügt.</p>
                <p>Hiermit akzeptiere(n) ich (wir) das Angebot, die Lizenzvereinbarung sowie die Bestimmungen und
    Hinweise in den Anmerkungen zum Angebot und erteilen hiermit
    den entsprechenden Auftrag.</p>
              </td>
            </tr>
            <tr>
              <td style="padding-top: 8px;">
                <p>Datum | Unterschrift | Firmenstempel</p>
                <br><br><br>
                <p>Handelt es sich beim Auftrag um Lizenzen oder Hardware, so werden 100% des Rechnungsbetrags im
    Voraus fällig.
    Handelt es sich beim Auftrag um Dienstleistungen, so werden 50% des Rechnungsbetrags fällig mit
    Auftragserteilung und die restlichen 50% mit Fertigstellung.
    Das Zahlungsziel beträgt 7 Tage ab Rechnungsdatum.</p>
                <p>Vielen Dank für Ihren Auftrag.</p>
              </td>
            </tr>
          </table>
          <br>
          <table style="border-top: 1px solid #000;">
            <tr>
              <td width="22%">
                TecSee UG (haftungsbeschränkt) <br>
                Biberacherstr. 53 <br>
                74078 Heilbronn <br>
                Deutschland
              </td>
              <td width="18%">
                Telefon: 07131 390 2637 <br>
                Mobil: 0160 774 98 66 <br>
                buchhaltung@tecsee.de <br>
                www.tecsee.de
              </td>
              <td width="20%">
                Geschäftsführer: Saad Radany <br>
                AG Stuttgart HRB 761002 <br>
                Steuernummer: 6205/41954 <br>
                Ust-IdNr.: DE313057563
              </td>
              <td width="17%">
                TecSee UG (haftungsbeschränkt) <br>
                Sparkasse Heilbronn <br>
                BIC: HEISDE66XXX <br>
                BIC: HEISDE66XXX
              </td>
              <td width="23%">
                TecSee UG (haftungsbeschränkt) <br>
                Fidor Bank <br>
                BIC: FDDODEMMXXX <br>
                IBAN: DE65700222000020349964
              </td>
            </tr>
          </table>
    </div>
      </div>
    </div>
  </div>
</body>

</html>
