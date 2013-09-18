//
//  SubCateViewController.m
//  ipapp
//
//  Created by kelaocai on 13-9-18.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "SubCateViewController.h"

@interface SubCateViewController ()

@end

@implementation SubCateViewController


#define COLUMN 4
#define ROW_HEIGHT 70.0f
#define VIEW_WIDTH 80.0f
- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    //子目录总数
    int ttl_subitems=[self.subCates count];
    int rows = (ttl_subitems / COLUMN) + ((ttl_subitems % COLUMN) > 0 ? 1 : 0);
    //绘制子板块目录视图
    for(int i=0;i<ttl_subitems;i++){
        //子板块信息
        NSDictionary *subItem=[self.subCates objectAtIndex:i];
        int row=i/COLUMN;
        int column=i%COLUMN;
        UIView *view=[[UIView alloc] initWithFrame:CGRectMake(column*VIEW_WIDTH,ROW_HEIGHT*row, VIEW_WIDTH, ROW_HEIGHT)];
        view.backgroundColor=[UIColor clearColor];
        [self.view addSubview:view];
        //创建子板块图标按钮
        UIButton *btn=[[UIButton alloc] initWithFrame:CGRectMake(15, 15, 50, 50)];
        [btn setBackgroundImage:[UIImage imageNamed:[[subItem objectForKey:@"img"] stringByAppendingFormat:@".png"]]
                       forState:UIControlStateNormal];
        btn.tag = i;
        //按钮添加事件，交给主目录控制器处理
        [btn addTarget:self.cateVc
                action:@selector(subCateBtnAction:)
        forControlEvents:UIControlEventTouchUpInside];

        [view addSubview:btn];
        [btn release];
        //创建子板块标题
        UILabel *lb=[[UILabel alloc] initWithFrame:CGRectMake(0, 65, 80, 14)];
        lb.textAlignment=NSTextAlignmentCenter;
        lb.font=[UIFont systemFontOfSize:12.0f];
        lb.textColor = [UIColor colorWithRed:204/255.0
                                       green:204/255.0
                                        blue:204/255.0
                                       alpha:1.0];
        lb.backgroundColor = [UIColor clearColor];
        
        lb.text=[subItem objectForKey:@"name"];
        [view addSubview:lb];
        [lb release];
        [view release];
        //重新整理self.view frame高度
        CGRect viewFrame=self.view.frame;
        viewFrame.size.height=ROW_HEIGHT*rows+20;
        self.view.frame=viewFrame;
    }
    
    //子板块view
    //UIView *subView
    
}



- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)dealloc{
    
    [_cateVc release];
    [_subCates release];
    [super dealloc];
}

@end
