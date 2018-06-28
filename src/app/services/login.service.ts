import { Injectable } from "@angular/core";
import "rxjs/add/operator/catch";
import "rxjs/add/operator/map";
import PostService from "./post.service";
import { apiUrl } from "../constants";

@Injectable()
export class LoginService extends PostService {
  url = apiUrl + "loginservice.php";
}
