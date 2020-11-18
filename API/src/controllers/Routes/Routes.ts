import Axios, { AxiosResponse } from 'axios';

export default class Routes {
  async getRoutes(): Promise<any> {
    const response: AxiosResponse<any> = await Axios({
      baseURL: 'http://vuejs.local-dev.shopworks.nl/',
      method: 'get',
      url: '/api/v3/_info/openapi3.json',
    });
    return response;
  }
}

import {
  Controller,
  Get,
  Post,
  Response,
  Request,
  ContentType,
} from '@tsed/common';
import Shopworks from '../Shopworks/Functions';

const routes = new Routes();
const shopworks = new Shopworks();

interface IData {
  paths: any;
  data: any;
}

@Controller('/api')
export class ShopworksController {
  @Get('/token')
  @ContentType('json')
  async getToken(request: Request, response: Response): Promise<any> {
    const data = await shopworks.getOath();
    return data;
  }
}
