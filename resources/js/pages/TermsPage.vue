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

  <UContainer
    as="section"
    class="flex flex-col gap-5.5 px-4 py-6 sm:px-7 sm:py-9"
  >
    <h1 class="font-sans text-3xl font-semibold tracking-tight text-neutral-50">Terms & Conditions</h1>

    <div
      class="flex flex-col gap-4 font-sans text-[15px] leading-relaxed text-neutral-400 [&_a]:text-identity-500 [&_a]:underline [&_a]:underline-offset-2 [&_h2]:mt-8 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:tracking-tight [&_h2]:text-neutral-50"
    >
      <h2>Use at your own risk</h2>
      <p>
        This website, and any projects or packages I publish, are provided "as is", without warranty of any kind. I put this work out
        because I enjoy building it and hope it's useful to others — not as a guaranteed or supported product. Use of this site and anything
        I build is entirely at your own risk, and I accept no liability for any damage, loss, or issues that may result from using them.
      </p>

      <h2>No tracking</h2>
      <p>
        I'm not interested in tracking visitors. This site does not place tracking cookies, analytics scripts, or any other third-party
        trackers.
      </p>

      <h2>Changes</h2>
      <p>These terms may be updated from time to time. Continued use of this site after a change means you accept the update.</p>
    </div>
  </UContainer>
</template>
