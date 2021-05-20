<template>
<div id="printMe" style="font-size: 3em;">
    <table width="100%">
        <tr class="mt-5">
            <td class="text-center align-middle" valign="top" width="50%">
                <img class="mt-4" :src="'/frontend/img/offer/bar_code.png'" alt="" />
                <h4>TecSee</h4>
            </td>
            <td align="right" class="text-center" width="50%">
                <img :src="'/frontend/img/offer/tecsee_logo.png'" alt="" width="88%" />
                <h3 class="text-gray">{{$t('ContractPage.moreOption')}}</h3>
            </td>
        </tr>
      </table>
      <table width="100%">
            <tr>
                  <td width="50%" align="left" class="text-left">
                        <p class="small-text"><small><strong>TecSee UG (haftungsbeschränkt), Biberacherstr. 53, 74078 Heilbronn</strong></small></p>
                  </td>
            </tr>
            <tr>
                  <td width="50%" align="left" class="text-left align-top">
                        <p class="text-left">
                              {{offer.client.name}} <br>
                              <template v-if="offer.client.metadata">
                                <template v-if="offer.client.metadata.first_name && offer.client.metadata.last_name">
                                  <template v-if="offer.client.metadata.gender == 'male'">
                                    {{$t('ContractPage.mr')}} {{offer.client.metadata.first_name}} {{offer.client.metadata.last_name}} <br>
                                  </template>
                                  <template v-else-if="offer.client.metadata.gender == 'female'">
                                    {{$t('ContractPage.mrs')}} {{offer.client.metadata.first_name}} {{offer.client.metadata.last_name}} <br>
                                  </template>
                                  <template v-else>
                                    {{$t('ContractPage.mr/mrs')}} {{offer.client.metadata.first_name}} {{offer.client.metadata.last_name}} <br>
                                  </template>
                                </template>
                                <template v-else-if="offer.client.metadata.first_name">
                                  <template v-if="offer.client.metadata.gender == 'male'">
                                    {{$t('ContractPage.mr')}} {{offer.client.metadata.first_name}} <br>
                                  </template>
                                  <template v-else-if="offer.client.metadata.gender == 'female'">
                                    {{$t('ContractPage.mrs')}} {{offer.client.metadata.first_name}} <br>
                                  </template>
                                  <template v-else>
                                    {{$t('ContractPage.mr/mrs')}} {{offer.client.metadata.first_name}} <br>
                                  </template>
                                </template>
                                <template v-else-if="offer.client.metadata.last_name">
                                  <template v-if="offer.client.metadata.gender == 'male'">
                                    {{$t('ContractPage.mr')}} {{offer.client.metadata.last_name}} <br>
                                  </template>
                                  <template v-else-if="offer.client.metadata.gender == 'female'">
                                    {{$t('ContractPage.mrs')}} {{offer.client.metadata.last_name}} <br>
                                  </template>
                                  <template v-else>
                                    {{$t('ContractPage.mr/mrs')}} {{offer.client.metadata.last_name}} <br>
                                  </template>
                                </template>
                                <template v-if="offer.client.metadata.street_number">
                                  {{offer.client.metadata.street_number}} <br>
                                </template>
                                <template v-if="offer.client.metadata.address">
                                  {{offer.client.metadata.address}} <br>
                                </template>
                              </template>
                        </p>
                  </td>
                  <td width="50%" align="left" class="text-left">
                        <p class="text-left pl-5">
                              TecSee UG (haftungsbeschränkt) <br>
                              {{offer.created_by.metadata.first_name}} {{offer.created_by.metadata.last_name}} <br>
                              Biberacherstr. 53 <br>
                              74078 Heilbronn <br>
                              Tel: 07131 390 2637 <br>
                              Web: www.tecsee.de <br>
                              E-Mail: {{offer.created_by.email}} <br>
                              Bearbeiter: {{offer.created_by.metadata.first_name}} {{offer.created_by.metadata.last_name}}
                        </p>
                  </td>
            </tr>
        </thead>
        <div class="row">
          <div class="col text-center">
            <h1>Angebotspreis</h1>
          </div>
        </div>
      </table>
      <table width="100%">
             <thead class="text-center">
                   <tr class="pb-2 mb-4" style="border-bottom: 1px solid #000;">
                     <th>{{$t('ContractPage.amount')}}</th>
                     <th>{{$t('ContractPage.designation')}}</th>
                     <th></th>
                     <th>{{$t('ContractPage.unitPrice')}}</th>
                     <th>{{$t('ContractPage.totalPrice')}}</th>
                   </tr>
             </thead>
             <tbody class="text-center">
                   <span v-for="item in offer.items.data" :key="item.id">
                         <tr class="pt-2">
                           <td>{{item.qty}}</td>
                           <td class="text-left" style="word-wrap: break-word;min-width: 300px;max-width: 300px;">{{item.name}}</td>
                           <td></td>
                           <td class="text-right">{{item.price}}</td>
                           <td class="text-right">{{item.total_price}}</td>
                         </tr>
                         <tr>
                           <td></td>
                           <td colspan="2" style="word-wrap: break-word;min-width: 300px;max-width: 300px;" class="text-left pl-5">** {{item.description}}</td>
                           <td></td>
                           <td></td>
                         </tr>
                   </span>
             </tbody>
             <tfoot class="text-right">
                   <tr>
                         <td></td>
                         <td></td>
                         <td>zzgl. 19,00% MwSt. in EUR</td>
                         <td></td>
                         <td>{{offer.total_tax}}</td>
                   </tr>
                   <tr style="border-bottom: 1px solid #000;">
                        <td></td>
                        <td></td>
                        <td><strong>{{$t('ContractPage.totalGroos')}}</strong></td>
                        <td></td>
                        <td><strong>{{offer.total}}</strong></td>
                   </tr>
             </tfoot>
      </table>
      <table width="100%">
        <tr>
            <td style="border-bottom: 1px solid #000; padding-top: 8px;">
                <p>{{$t('ContractPage.framework')}}</p>
                <p>{{$t('ContractPage.accept')}}</p>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 8px;">
                <p>{{$t('ContractPage.dateSigCompany')}}</p>
                <br><br><br>
                <p>{{$t('ContractPage.orderLicense')}}</p>
                <p>{{$t('ContractPage.thanks')}}</p>
            </td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr class="mt-5">
            <td align="center" class="text-center" width="50%">
                <img :src="'/frontend/img/brand/blue.png'" alt="" width="20%" />
            </td>
        </tr>
    </table>
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
</template>
<script>
export default {
    name: 'PrintOfferComponent',
    props: {
        offer: Object
    },
}
</script>
<style scoped>
table {
    font-size: 5em;
}

tfoot tr td {
    font-weight: bold;
    font-size: x-small;
}

.gray {
    background-color: lightgray
}

.title {
    font-size: 2em;
}
</style>
