<template>
  <Modal :show="show">
    <div
      class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
    >
      <slot>
        <ModalHeader>{{ __("Created Token") }}</ModalHeader>
        <ModalContent>
          <div class="flex flex-col">
            <div class="flex flex-col space-y-2">
              <div class="flex items-center justify-between space-x-2">
                <input
                  readonly
                  class="w-full form-control form-input"
                  type="text"
                  :value="newToken"
                  data-disabled
                />
                <Button
                  type="button"
                  variant="action"
                  @click.prevent.stop="copyValueToClipboard"
                >
                  <Icon
                    v-tooltip="{
                      content: __('Copied to clipboard'),
                      triggers: ['click'],
                      placement: 'right',
                      autoHide: true,
                    }"
                    name="clipboard"
                    type="solid"
                  />
                </Button>
              </div>
              <HelpText class="mt-2 help-text-error">
                {{
                  __(
                    "Make sure to copy your new personal access token now. You won't be able to see it again!"
                  )
                }}
              </HelpText>
            </div>
          </div>
        </ModalContent>
      </slot>

      <ModalFooter>
        <div class="ml-auto">
          <NovaButton type="button" @click.prevent="handleConfirmed">
            {{ __("Confirm") }}
          </NovaButton>
        </div>
      </ModalFooter>
    </div>
  </Modal>
</template>

<script>
import { Button as NovaButton, Icon } from "laravel-nova-ui";
export default {
  components: {
    NovaButton,
    Icon,
  },
  props: {
    newToken: {
      required: true,
      type: String,
    },
    show: { type: Boolean, default: false },
  },
  emits: ["confirmed"],
  methods: {
    handleConfirmed() {
      this.$emit("confirmed");
    },
    copyValueToClipboard() {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(this.newToken);
      } else if (window.clipboardData) {
        window.clipboardData.setData("Text", this.newToken);
      } else {
        const input = document.createElement("input");
        const [scrollTop, scrollLeft] = [
          document.documentElement.scrollTop,
          document.documentElement.scrollLeft,
        ];
        document.body.appendChild(input);
        input.value = this.newToken;
        input.focus();
        input.select();
        document.documentElement.scrollTop = scrollTop;
        document.documentElement.scrollLeft = scrollLeft;
        document.execCommand("copy");
        input.remove();
      }
    },
  },
};
</script>
