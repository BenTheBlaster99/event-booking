<script>
import CardEventList from '@/components/CardEventList.vue';
export default {
    name: 'ProfileEvents',
    components: {
        CardEventList
    },
    data() {
        return {
            profileEvents: [
                {
                    eventID: '2',
                    imageUrl: 'https://placehold.co/300x200/E0F7E0/2C502C?text=Event+2',
                    dateDay: '1',
                    dateMonth: 'MAY',
                    title: 'Jo Malone London\'s Mother\'s Day',
                    location: 'Radius Gallery, Santa Cruz',
                    time: '2:00 PM'
                },
                {
                    eventID: '3',
                    imageUrl: 'https://placehold.co/300x200/F7F0E0/504A2C?text=Event+3',
                    dateDay: '1',
                    dateMonth: 'MAY',
                    title: 'Women\'s leadership conference',
                    location: 'Conference Center, NY',
                    time: '2:00 PM'
                }
            ]
        }
    }
}
</script>

<template>
    <div class="p-4 bg-gray-50 space-y-4">
        <CardEventList 
            v-for="event in profileEvents"
            :key="event.eventID"
            :eventID = "event.eventID"
            :imageUrl = "event.imageUrl"
            :dateDay="event.dateDay"
            :dateMonth="event.dateMonth"
            :title="event.title"
            :location="event.location"
            :time="event.time"
        />
        <p v-if="profileEvents.length === 0" class="text-center text-gray-600">No evets found for this profile. </p>
    </div>

</template>