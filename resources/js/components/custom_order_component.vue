<template>
    <form id="custom-order" class="col-sm-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3">
        <h4>Egyedi matrica árajánlat kérés</h4>
        <label v-html="label"></label>
        <input v-if="!this.sent" id="custom-order-name" v-model="customOrderName" placeholder="Név" type="text" class="form-control col-sm-12" required>
        <input v-if="!this.sent" id="custom-order-email" v-model="customOrderEmail" placeholder="E-mail cím" type="email" class="form-control col-sm-12" required>
        <textarea v-if="!this.sent" id="custom-order-text" v-model="customOrderText" placeholder="Üzenet" class="form-control col-sm-12" required></textarea>
        <button v-if="!this.sent" id="send-message-button" class="button" v-on:click.prevent="sendcustomOrderEmail"><i class="fa fa-paper-plane"></i> Küldés</button>
        <div id="success-text" v-if="this.sent">
            Köszönjük az árajánlat kérést, hamarosan válaszolunk email-ben.
        </div>
    </form>
</template>

<script>
    export default {
        props: {
        },
        data: function () {
            return {
                sent: false,
                label: 'Írd le röviden, hogy milyen matricát szeretnél az alábbi adatok megadásával:<br><ul><li>Milyen méretben szeretnéd</li><li>Milyen színű legyen</li></ul>Ha van bármilyen mintád, akkor küld el emailen nekünk, s hamarosan felvesszük veled a kapcsolatot.',
                customOrderName: '',
                customOrderEmail: '',
                customOrderText: '',
            };
        },
        methods: {
            sendcustomOrderEmail: function() {
                axios.post('/send-custom-order-message', {
                    name: this.customOrderName,
                    email: this.customOrderEmail,
                    text: this.customOrderText,
                }).then(response => {
                    this.sent = true;
                    this.label = "Sikeres üzenetküldés!"
                }).catch(error  => {
                    this.label = "Hiba!"
                });
            }
        },
    }
</script>