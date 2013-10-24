//
//  NSDictionary+Json.m
//  ipapp
//
//  Created by kelaocai on 13-10-23.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "NSDictionary+Json.h"

@implementation NSDictionary (Json)

// 直把远程的地址上Json数据转,换成Dictionary对象
+(NSDictionary*)dictionaryWithContentsOfURLString:(NSString*)urlAddress
{
    // 请求远程数据，存放到NSData对象中
    NSData* data =[NSData dataWithContentsOfURL:[NSURL URLWithString: urlAddress]];
    
    // 定义一个错误信息的对象
    __autoreleasing NSError *error =nil;
    
    // 序列化字符串
    id result =[NSJSONSerialization JSONObjectWithData:data
                                               options:kNilOptions error:&error];
    if(error !=nil)
        return nil;
    
    return result;
}

// 把当前的Dictionary对象,转成Json对象
-(NSData*)toJSON{
    NSError *error =nil;
    // 把当前的Dictionary对象转换成字符串
    id result =[NSJSONSerialization dataWithJSONObject:self
                                               options:kNilOptions error:&error];
    if(error !=nil)
        return nil;
    
    return result;
}


@end
