<template>
    <el-timeline class="pl-0">
        <el-timeline-item v-for="comment in items"
            :key="comment.id"
            >
           {{ comment.comment }}
           <p style="color: #7d878a">
                <span >{{ comment.created_at}}</span>
                <b>{{ comment.first_name}} {{ comment.last_name }}</b>
            </p>
        </el-timeline-item>

        <el-timeline-item>&nbsp;</el-timeline-item>
    </el-timeline>
</template>

<script>

export default {
    props: {
        items: {
            type: Array,
            required: true
        }
    }
}

</script>