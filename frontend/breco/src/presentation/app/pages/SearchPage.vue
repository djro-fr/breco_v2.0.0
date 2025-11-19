<script setup lang="ts">
import { useAuthStore } from '@/presentation/features/auth/stores/authStore'
const authStore = useAuthStore()
</script>

<template>
  <div class="background"></div>
  <main>
      <h1>Breco</h1>
      <section id="mainSection">
          <div id="searchWindow">
              <h2>Vous recherchez</h2>
              <form action="#">
                  <div id="trajet" class="whiteWindow">
                      <div id="Depart">
                          <div>
                              <label for="villeDepart">Depuis</label>
                              <input type="text" id="villeDepart" name="villeDepart" autocomplete="off">
                              <ul id="listeDepart"></ul>
                          </div>
                          <div>
                              <label for="heureDepart">À</label>
                              <input type="text" id="heureDepart" name="heureDepart" autocomplete="off" placeholder="00:00" maxlength="5">
                          </div>
                      </div>
                      <div id="Duree"></div>
                      <div id="Arrivee">
                          <div>
                              <label for="villeArrivee">Vers</label>
                              <input type="text" id="villeArrivee" name="villeArrivee" autocomplete="off">
                              <ul id="listeArrivee"></ul>
                          </div>
                          <div>
                              <label for="heureArrivee">À</label>
                              <input type="text" id="heureArrivee" name="heureArrivee" autocomplete="off" placeholder="00:00" maxlength="5">
                          </div>
                      </div>
                  </div>
                  <div id="jours" class="whiteWindow">
                  <input type="checkbox" id="J1" name="J1" ><label for="J1">Lun</label>
                  <input type="checkbox" id="J2" name="J2" ><label for="J2">Mar</label>
                  <input type="checkbox" id="J3" name="J3" ><label for="J3">Mer</label>
                  <input type="checkbox" id="J4" name="J4" ><label for="J4">Jeu</label>
                  <input type="checkbox" id="J5" name="J5" ><label for="J5">Ven</label>
                  <input type="checkbox" id="J6" name="J6" ><label for="J6">Sam</label>
                  <input type="checkbox" id="J7" name="J7" ><label for="J7">Dim</label>
                  </div>
                  <button type="submit" class="btn-action">Rechercher</button>
              </form>
          </div>
      </section>
  </main>
</template>


<style scoped>
main{
    position: absolute;
    top: 0;
    display: block;
    height: 100%;
    width: 100%;
    overflow: hidden;
    background-color: rgba(0, 0, 0, 0.5);
    h1{
        text-align: center;
        margin: 0;
        font-size: var(--fontXXL);
        line-height: var(--fontXXL);
        display: block;
        color:white;
        padding-top: 10vh ;
    }
    section#mainSection{
        height: 100%;
        width: 100%;
        div#searchWindow{
            position: absolute;
            height: 65%;
            width: 100%;
            max-width: 360px;
            margin-left: calc(50% - 180px);
            bottom: 0;
            display: block;
            background-color: var(--back-gray) ;
            border-radius: 20px 20px 0 0 ;
            h2{
                font-size: var(--fontXL);
                text-align: center;
                margin: 15px 0 15px;
            }
            form{
                padding: 0 20px 10px;
                div#trajet{
                    padding: 0;
                    width: 100%;
                    margin: 0 auto;
                    max-width: 360px;
                    position: relative;
                    >div{
                        width: 100%;
                        padding: 20px;
                        display: flex;
                        flex-direction:row;
                        >div{
                            width: 100%;
                            position: relative;
                            display: flex;
                            flex-direction: column;
                            label{
                                color: var(--green) ;
                                font-size: var(--fontXS);
                                font-weight: 600;
                            }
                            input{
                                color: var(--dark-gray);
                                /* background-color: var(--back-gray); */
                                border: none;
                                font-size: var(--fontM);
                                padding-top: 2px;
                                height: 25px;
                                width: 95%;
                                border-bottom: 2px solid var(--back-gray);
                            }
                            input:focus{
                                outline: none;
                                border-bottom: 2px solid var(--green);
                            }
                            input#villeDepart, input#villeArrivee{
                                font-weight: 600;
                                width: 90%;
                            }
                            ul#listeDepart, ul#listeArrivee{
                                display: none;
                                position: absolute;
                                z-index: 2;
                                box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.2);
                                top:37px;
                                background-color: white;
                                padding-left: 0;

                                width: calc(90% + 4px);
                                li{
                                    padding: 10px 10px;
                                    list-style: none;
                                    font-size:  var(--fontS);
                                }
                                li:hover{
                                    background-color: var(--dark-white);
                                    color: var(--green);
                                    cursor: pointer;
                                }
                            }
                        }
                        >div:first-child{
                            width: 75%;
                        }
                        >div:last-child{
                            width: 25%;
                        }
                    }
                    div#Depart{
                        border-bottom: 1px solid var(--dark-gray);
                        width: calc(100% - 40px);
                        padding-bottom: 30px;
                    }
                    div#Duree{
                        position: absolute;
                        font-size: var(--fontS);
                        display: inline;
                        padding: 5px 0 5px;
                        top: calc(50% - 15px);
                        left: 33%;
                        text-align: center;
                        height: 20px;
                        width: 34%;
                        border-radius: 50px;
                        background-color:  var(--dark-gray);
                        color: white;
                        font-weight: 600;
                    }
                    div#Arrivee{
                        width: calc(100% - 40px);
                        padding-bottom: 30px;
                    }
                }
                div#jours{
                    padding: 10px;
                    margin-top: 20px;
                    display: flex;
                    justify-content: space-between;
                    input[type="checkbox"]{
                        display: none;
                    }
                    input[type="checkbox"] + label {
                        cursor: pointer;
                        border-bottom: 1px dotted var(--light-gray);
                        font-size:  var(--fontM);
                        color: var(--dark-gray);
                        font-weight: 500;

                    }
                    input[type="checkbox"]:checked + label {
                        color: var(--primary-color);
                        border-bottom: 1px solid var(--primary-color);
                    }
                }
                button.search{
                    margin-top: 20px;
                    width: 100%;
                    height: 44px;
                    border: 0;
                    border-radius: 22px;
                    background-color: var(--primary-color);
                    letter-spacing: .02em;
                    color: black;
                    font-size: var(--fontXL);
                    line-height: 1.65;
                    box-shadow: 0 3px 2px 0 var(--primary-color-dark);
                }

            }
        }
    }
}

</style>
