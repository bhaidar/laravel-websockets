<script setup>
import { ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const chats = ref([]);
const users = ref([]);
const newMessage = ref('');
const whisperMessage = ref('');
let whisper_typing = false;


const handleChat = () => {
    let message = newMessage.value.trim();

    if (!message) {
        return;
    }

    axios.post(route('chat.store'), { message }, {
            'Accept': 'text/json',
    })
        .then(response => response?.data)
        .then((response) => {
            chats.value.push({
                ...response,
                'sender': '',
                'direction': 'justify-end'
            });
            newMessage.value = '';
        })
        .catch(error => {
            console.error(error);
        })
        .finally(() => {});
}

let groupChannel = window.Echo.join('group-chat');

groupChannel
    .here((allUsers) => {
        // fires for others when they join
        console.log('here: ', allUsers);
        allUsers?.forEach((user) => {
            users.value.push(user);
        });
    })
    .joining((user) => {
        // fires for me when another user is joining
        console.log('joining: ', user);
        users.value.push(user);
    })
    .leaving((user) => {
        users.value = users.value.filter(u => u.id !== user.id);
    })
    .listenForWhisper('typing', (e) => {
        console.log('Event typing', e);
        if ( e.typing ) {
            whisperMessage.value = `${e.email} is typing ...`;
        } else {
            whisperMessage.value = '';
        }
    })
    .listen('.group-message-received', (e) => {
        console.log(e);
        chats.value.push({
        ...e,
        'direction': 'justify-start'
    });
});

const handleTyping = (e) => {
    const user = usePage().props.auth.user;

    // user didn't hit Enter and there is some text typed
    if ( e.keyCode != 13 && newMessage.value?.trim().length != 0 ) {
        // already whispered to others
        if ( whisper_typing ) {
            return;
        }

        whisper_typing = true;
        // whisper to others
        groupChannel.whisper('typing', {
                email: user.email,
                typing: true
        });
    } else {
        whisper_typing = false;

        // submitted the message, not typing anymore
        groupChannel.whisper('typing', {
            email: user.email,
            typing: false
        });
    }
}

</script>

<template>

    <Head>
      <title>Group Chat</title>
    </Head>

    <AuthenticatedLayout>
        <div class="flex flex-col min-h-full my-8">
            <div class="grid place-content-center">
                <div class="min-w-[400px] rounded border bg-gray-200">
                    <div>
                    <div class="w-full">

                        <!-- Chat Header -->
                        <div class="relative flex items-center border-b border-gray-300 p-3">
                        <div class="flex-1">
                            <span class="ml-2 block font-bold text-gray-600">{{ $page.props.auth.user.name }}</span>
                            <span class="ml-2 block font-normal text-gray-400">({{ $page.props.auth.user.email }})</span>
                        </div>
                        <div class="justify-end space-x-2 max-w-xs">
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="user in users" :key="user.id"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-500"
                                >
                                    <span :title="user.name" class="font-medium leading-none text-white">{{ user.initials }}</span>
                                </span>
                            </div>
                        </div>
                        </div>

                        <!-- Chat Content -->
                        <div class="relative flex flex-col justify-between h-[40rem] w-full overflow-y-auto p-1">
                            <ul class="space-y-2 p-6">
                                <li class="flex justify-start" v-for="chat in chats" :key="chat.user_id" :class="chat.direction">
                                <div class="relative max-w-xl bg-gray-100 rounded px-4 py-2 text-gray-700 shadow">
                                    <div class="block" v-if="chat.sender">
                                        <span class="font-bold text-xl text-gray-600">{{ chat.sender }}</span>
                                    </div>
                                    <span class="block">{{ chat.message }}</span>
                                    <span class="flex justify-end text-xs text-gray-400 -mr-3">{{ chat.time }}</span>
                                </div>
                                </li>
                            </ul>
                            <span class="block w-full text-gray-400 text-base">{{ whisperMessage }}</span>
                        </div>

                        <!-- Chat Prompt -->
                        <div class="flex w-full items-center justify-between border-t border-gray-300 p-3">
                            <input
                                type="text"
                                placeholder="Type your message. Press enter to send."
                                v-model="newMessage"
                                @keyup.enter="handleChat"
                                @keyup="handleTyping"
                                class="mx-3 block w-full rounded-full bg-gray-100 py-2 pl-4 outline-none focus:text-gray-700"
                                name="message" required />
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
