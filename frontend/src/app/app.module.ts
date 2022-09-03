import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HeaderComponent } from './component/header/header.component';
import { FooterComponent } from './component/footer/footer.component';
import { LandscapeComponent } from './component/landscape/landscape.component';
import { HomeComponent } from './component/home/home.component';
import { HomeCorpusComponent } from './component/home-corpus/home-corpus.component';
import { HomeHeadlineComponent } from './component/home-headline/home-headline.component';
import { HomeGraphComponent } from './component/home-graph/home-graph.component';

@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    FooterComponent,
    LandscapeComponent,
    HomeComponent,
    HomeCorpusComponent,
    HomeHeadlineComponent,
    HomeGraphComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
