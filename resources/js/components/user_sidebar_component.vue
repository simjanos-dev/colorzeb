<template>
    <div id="side-panel" class="col-sm-12 col-md-8 col-lg-3 col-xl-2 offset-md-2 offset-lg-0 box-shadow">
        <div id="header"><i class="fa fa-user"></i>&nbsp;&nbsp;{{ _userName }}</div>

        <a v-for="(link, index) in links" :key="index" :href="link.location"><button :class="{'active': active == link.text, 'button': true, 'blue': true, 'admin-menu-button': true}">{{ link.text }}</button></a>
    </div>
</template>

<script>
    export default {
        props: {
            _active: String,
            _userName: String,
        },
        data: function() {
            return {
                links: [
                    {
                        text: 'Adataim',
                        location: '/user/data',
                    },
                    {
                        text: 'Megrendeléseim',
                        location: '/user/orders',
                    },
                ],
                active: this.$props._active,
            }
        },
        
        mounted: function() {
            for (var i = 0; i < this.links.length; i++) {
                if (this.links[i].text == this.active) {
                    return;
                }
            }

            this.links.push({
                text: this.active,
                link: window.location,
            });
        },
        
    }
</script>