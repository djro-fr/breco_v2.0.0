<script setup lang="ts"></script>

<template>
  <div class="background"></div>
  <main class="absolute top-0 block h-full w-full overflow-hidden">
    <div class="flex flex-col h-[35%] items-center justify-center">
      <h1 class="text-6xl pb-6 text-white leading-10">Breco</h1>
      <h2 class="m-0 p-0 text-3xl text-white leading-5 text-center font-normal">
        Covoiturage en&nbsp;Bretagne
      </h2>
    </div>
    <div
      id="searchWindow"
      class="absolute flex flex-col justify-center h-[65%] w-full bottom-0 bg-white rounded-t-5xl mx-auto"
    >
      <h3 class="pt-0 mb-4 mx-0 text-primary-dark text-3xl text-center font-medium">Vous recherchez</h3>
      <form action="#" class="pt-0 px-5 py-3 pb-3 max-w-3xl mx-auto">
        <div id="trajet" class="relative my-0 mx-auto p-0 w-full">
          <div id="Depart" class="flex flex-row w-full pb-7">
            <div class="relative w-full pr-[5vw] sm:pr-6 flex flex-col">
              <label for="villeDepart" class="text-primary-dark text-md font-semibold"
                >Depuis</label
              >
              <input type="text" id="villeDepart" name="villeDepart" autocomplete="off" />
              <ul
                id="listeDepart"
                class="hidden absolute z-2 w-[calc(90%+4px)] shadow-window t-[37px] bg-white pl-0"
              ></ul>
            </div>
            <div class="relative w-20 flex flex-col">
              <label for="heureDepart" class="text-primary-dark text-md font-semibold">À</label>
              <input
                type="text"
                id="heureDepart"
                name="heureDepart"
                autocomplete="off"
                placeholder="00:00"
                maxlength="5"
              />
            </div>
          </div>
          <div id="Arrivee" class="flex flex-row w-full mb-10">
            <div class="relative w-full pr-[5vw] sm:pr-6 flex flex-col">
              <label for="villeArrivee" class="text-primary-dark text-md font-semibold">Vers</label>
              <input type="text" id="villeArrivee" name="villeArrivee" autocomplete="off" />
              <ul
                id="listeArrivee"
                class="hidden absolute z-2 w-[calc(90%+4px)] shadow-window t-[37px] bg-white pl-0"
              ></ul>
            </div>
            <div class="relative w-20 flex flex-col">
              <label for="heureArrivee" class="text-primary-dark text-md font-semibold">À</label>
              <input
                type="text"
                id="heureArrivee"
                name="heureArrivee"
                autocomplete="off"
                placeholder="00:00"
                maxlength="5"
              />
            </div>
          </div>
        </div>
        <div id="jours" class="mt-5 flex justify-center gap-3 flex-wrap">
          <input type="checkbox" class="hidden" id="J1" name="J1" /><label
            for="J1"
            class="px-5 py-3 cursor-pointer text-primary-dark border-2 border-primary-light rounded-lg font-bold"
            >Lu</label
          >
          <input type="checkbox" class="hidden" id="J2" name="J2" /><label
            for="J2"
            class="px-5 py-3 cursor-pointer text-primary-dark border-2 border-primary-light rounded-lg font-bold"
            >Ma</label
          >
          <input type="checkbox" class="hidden" id="J3" name="J3" /><label
            for="J3"
            class="px-5 py-3 cursor-pointer text-primary-dark border-2 border-primary-light rounded-lg font-bold"
            >Me</label
          >
          <input type="checkbox" class="hidden" id="J4" name="J4" /><label
            for="J4"
            class="px-5 py-3 cursor-pointer text-primary-dark border-2 border-primary-light rounded-lg font-bold"
            >Je</label
          >
          <input type="checkbox" class="hidden" id="J5" name="J5" /><label
            for="J5"
            class="px-5 py-3 cursor-pointer text-primary-dark border-2 border-primary-light rounded-lg font-bold"
            >Ve</label
          >
          <input type="checkbox" class="hidden" id="J6" name="J6" /><label
            for="J6"
            class="px-5 py-3 cursor-pointer text-primary-dark border-2 border-primary-light rounded-lg font-bold"
            >Sa</label
          >
          <input type="checkbox" class="hidden" id="J7" name="J7" /><label
            for="J7"
            class="px-5 py-3 cursor-pointer text-primary-dark border-2 border-primary-light rounded-lg font-bold"
            >Di</label
          >
        </div>
        <button type="submit" class="btn-action mt-12 mb-4">Rechercher</button>
      </form>
    </div>
  </main>
</template>

<style scoped>
#listeDepart li,
#listeArrivee li {
  padding: 10px;
  list-style: none;
  font-size: var(--fontS);
}
#listeDepart li:hover,
#listeArrivee li:hover {
  background-color: var(--dark-white);
  color: var(--green);
  cursor: pointer;
}

#jours input[type='checkbox']:checked + label  {
  background-color: var(--color-primary-light);
  border-color: var(--color-primary-light);
  color: black;
}
#jours input[type='checkbox']:hover + label  {
  background-color: var(--color-primary-light50);
  color: var(--text-primary-dark);
}
</style>
