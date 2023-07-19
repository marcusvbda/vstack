<!-- eslint-disable max-len -->
<template>
    <div class="hidden md:block">
        <nav
            class="flex px-5 py-3 text-gray-700 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-none"
            aria-label="Breadcrumb"
        >
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center px-4">
                    <template v-for="(item, index) in items">
                        <a
                            :key="index"
                            :href="item.route"
                            v-if="index != items.length - 1"
                            :class="[
                                'inline-flex items-center text-sm font-medium  hover:text-blue-600',
                                index != items.length - 1
                                    ? 'text-gray-700 dark:text-gray-50'
                                    : '',
                            ]"
                        >
                            <svg
                                v-if="index != 0"
                                aria-hidden="true"
                                class="w-6 h-6 text-gray-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            {{ item.title }}
                        </a>
                        <span
                            :class="[
                                'inline-flex items-center text-sm',
                                'font-medium text-blue-700',
                            ]"
                            :key="`${index}-last`"
                            v-else
                        >
                            <svg
                                v-if="index != 0"
                                aria-hidden="true"
                                class="w-6 h-6 text-gray-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            {{ item.title }}
                        </span>
                    </template>
                </li>
            </ol>
        </nav>
    </div>
</template>
<script>
export default {
    props: {
        items: {
            type: Array,
            default: () => [],
        },
    },
};
</script>
