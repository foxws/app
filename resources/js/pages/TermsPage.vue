<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Terms & Conditions">
    <meta
      head-key="description"
      name="description"
      content="Terms and conditions for using foxws.nl and its projects and packages."
    />
  </Head>

  <section class="bg-page-glow">
    <UContainer
      class="flex flex-col items-center px-4 pt-14 pb-10 text-center sm:px-7 sm:pt-20 sm:pb-12 lg:pt-26 lg:pb-16"
    >
      <h1
        class="font-sans text-5xl leading-[.9] font-bold tracking-[-.04em] text-neutral-50 [text-shadow:0_12px_48px_rgb(0_0_0/0.35)] sm:text-7xl lg:text-[7.5rem]"
      >
        <span class="block">Terms &amp;</span>
        <span class="block">Conditions</span>
      </h1>
    </UContainer>

    <UContainer class="px-4 pb-20 sm:px-7">
      <div
        class="mx-auto flex max-w-160 flex-col gap-7 font-sans text-base leading-relaxed text-pretty text-neutral-300 sm:text-lg"
      >
        <p>
          This website, and any projects or packages I publish, are provided "as is", without warranty of any kind. I
          put this work out because I enjoy building it and hope it's useful to others — not as a guaranteed or
          supported product. Use of this site and anything I build is entirely at your own risk, and I accept no
          liability for any damage, loss, or issues that may result from using them.
        </p>

        <p>
          I'm not interested in tracking visitors. This site does not place tracking cookies, analytics scripts, or any
          other third-party trackers.
        </p>

        <p>
          These terms may be updated from time to time. Continued use of this site after a change means you accept the
          update.
        </p>
      </div>
    </UContainer>
  </section>
</template>
