<template>
    <thead class="sortableTableHeader">
        <tr>
            <th v-for="option in options.list" :class="{sortableHeader: option.sortable, sortableHeaderActive: option.id == options.active}" @click="sortData(option)">
                {{ option.title }}
                <template v-if="option.sortable">
                    <i v-if="option.id != options.active || (options.asc && option.id == options.active)" class="material-icons">expand_more</i>
                    <i v-else class="material-icons">expand_less</i>
                </template>
            </th>
        </tr>
    </thead>
</template>

<script>
export default {
    props: ['options'],

    methods: {
        sortData(item) {
            this.$emit('sort', item)
        }
    }
}
</script>
